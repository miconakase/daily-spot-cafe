<?php
require_once __DIR__ . '/../model/Order.php';
require_once __DIR__ . '/../model/OrderItem.php';
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/ProductVariant.php';
require_once __DIR__ . '/../model/UserDetails.php';
require_once __DIR__ . '/NotificationController.php';

class OrderController
{
    // Payment methods a customer can pick at checkout
    const CUSTOMER_PAYMENT_TYPES = ['COD', 'QRPH']; // shown as "Cash on Pickup" and "QR PH"

    private $order;
    private $orderItem;
    private $product;
    private $variant;

    public function __construct()
    {
        $this->order     = new Order();
        $this->orderItem = new OrderItem();
        $this->product   = new Product();
        $this->variant   = new ProductVariant();
    }

    public function index()
    {
        $result = $this->order->read();
        $orders = array();

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }

    public function show($id)
    {
        $this->order->order_id = $id;
        $this->order->readOne();

        if (!empty($this->order->order_status)) {
            $orderItems = $this->getOrderItems($id);

            return array(
                'order_id'       => $this->order->order_id,
                'user_id'        => $this->order->user_id,
                'payment_status' => $this->order->payment_status,
                'payment_type'   => $this->order->payment_type,
                'reference_number' => $this->order->reference_number,
                'total_amount'   => $this->order->total_amount,
                'order_status'   => $this->order->order_status,
                'notes'          => $this->order->notes,
                'created_at'     => $this->order->created_at,
                'items'          => $orderItems
            );
        }

        return null;
    }

    public function store($data)
    {
        if (empty($data['items']) || !is_array($data['items'])) {
            return array('success' => false, 'message' => 'Order must contain at least one item');
        }

        $totalAmount = 0;
        $orderItems  = array();

        // Validate and calculate total
        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                return array('success' => false, 'message' => 'Invalid order item data');
            }

            $this->product->product_id = $item['product_id'];
            $this->product->readOne();

            if (empty($this->product->product_name)) {
                return array('success' => false, 'message' => 'Product not found: ' . $item['product_id']);
            }

            if (!$this->product->is_available || !$this->product->is_published) {
                return array('success' => false, 'message' => 'Product not available: ' . $this->product->product_name);
            }

            $quantity = (int)$item['quantity'];
            if ($quantity < 1 || $quantity > 99) {
                return array('success' => false, 'message' => 'Invalid quantity for ' . $this->product->product_name);
            }

            // Price comes from the database, never from the client
            $variantType = !empty($item['variant_type']) ? (string)$item['variant_type'] : null;
            $sizeLabel   = !empty($item['size_label'])   ? (string)$item['size_label']   : null;
            $unitPrice   = (float)$this->product->price;

            if ($variantType !== null || $sizeLabel !== null) {
                $variant = $this->variant->findOne((int)$item['product_id'], $variantType, $sizeLabel);
                if (!$variant || !$variant['is_available']) {
                    return array('success' => false, 'message' => 'Option not available for ' . $this->product->product_name);
                }
                $unitPrice = (float)$variant['price'];
            } elseif ($this->variant->readByProduct((int)$item['product_id'])->num_rows > 0) {
                // A drink with Hot/Iced or size options needs one chosen
                return array('success' => false, 'message' => 'Choose the options (Hot/Iced, size) for ' . $this->product->product_name);
            }

            $totalAmount += $quantity * $unitPrice;

            $orderItems[] = array(
                'product_id'   => $item['product_id'],
                'product_name' => $this->product->product_name,
                'variant_type' => $variantType,
                'size_label'   => $sizeLabel,
                'quantity'     => $quantity,
                'unit_price'   => $unitPrice
            );
        }

        // Create order
        $this->order->user_id        = $data['user_id']        ?? null;
        $this->order->payment_status = $data['payment_status'] ?? 'Pending';
        $this->order->payment_type   = $data['payment_type']   ?? 'COD';
        $this->order->reference_number = $data['reference_number'] ?? null;
        $this->order->total_amount   = $totalAmount;
        $this->order->order_status   = $data['order_status']   ?? 'Pending';
        $this->order->notes          = $data['notes']          ?? '';

        if ($this->order->create()) {
            $orderId = $this->order->order_id;

            // Create order items
            foreach ($orderItems as $item) {
                $this->orderItem->order_id    = $orderId;
                $this->orderItem->product_id  = $item['product_id'];
                $this->orderItem->variant_type = $item['variant_type'];
                $this->orderItem->size_label  = $item['size_label'];
                $this->orderItem->quantity    = $item['quantity'];
                $this->orderItem->unit_price  = $item['unit_price'];

                if (!$this->orderItem->create()) {
                    error_log("Failed to create order item for order $orderId");
                }
            }

            return array(
                'success'      => true,
                'message'      => 'Order created successfully',
                'order_id'     => $orderId,
                'total_amount' => $totalAmount
            );
        }

        return array('success' => false, 'message' => 'Failed to create order');
    }

    public function update($id, $data)
    {
        $this->order->order_id = $id;
        $this->order->readOne();

        if (empty($this->order->order_status)) {
            return array('success' => false, 'message' => 'Order not found');
        }

        $this->order->payment_status = $data['payment_status'] ?? $this->order->payment_status;
        $this->order->payment_type   = $data['payment_type']   ?? $this->order->payment_type;
        $this->order->total_amount   = $data['total_amount']   ?? $this->order->total_amount;
        $this->order->order_status   = $data['order_status']   ?? $this->order->order_status;
        $this->order->notes          = $data['notes']          ?? $this->order->notes;

        if ($this->order->update()) {
            return array('success' => true, 'message' => 'Order updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update order');
    }

    public function updateStatus($id, $status)
    {
        $validStatuses = ['Pending', 'Processing', 'Ready', 'Completed', 'Cancelled'];

        if (!in_array($status, $validStatuses)) {
            return array('success' => false, 'message' => 'Invalid order status');
        }

        $this->order->order_id = $id;
        $this->order->readOne();

        if (empty($this->order->order_status)) {
            return array('success' => false, 'message' => 'Order not found');
        }

        $this->order->order_status = $status;

        if ($this->order->update()) {
            return array('success' => true, 'message' => 'Order status updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update order status');
    }

    public function destroy($id)
    {
        // First delete order items
        $this->deleteOrderItems($id);

        // Then delete the order
        $this->order->order_id = $id;

        if ($this->order->delete()) {
            return array('success' => true, 'message' => 'Order deleted successfully');
        }

        return array('success' => false, 'message' => 'Failed to delete order');
    }

    public function getOrdersByStatus($status)
    {
        $result = $this->order->getOrdersByStatus($status);
        $orders = array();

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }

    // --- Admin actions ---

    /** Order with its items and the customer's contact details. */
    public function getAdminOrderDetails($id)
    {
        $order = $this->show((int) $id);
        if (!$order) {
            return array('success' => false, 'message' => 'Order not found');
        }

        $customer = null;
        if (!empty($order['user_id'])) {
            $details = new UserDetails();
            $details->user_id = $order['user_id'];
            $details->readOne();
            if ($details->first_name !== null) {
                $customer = array(
                    'name'  => $details->getFullName(),
                    'email' => $details->email,
                    'phone' => $details->phone_number,
                );
            }
        }
        $order['customer'] = $customer;

        return array('success' => true, 'data' => $order);
    }

    public function markPaid($id)
    {
        $this->order->order_id     = (int) $id;
        $this->order->order_status = null;
        $this->order->readOne();

        if (empty($this->order->order_status)) {
            return array('success' => false, 'message' => 'Order not found');
        }
        if ($this->order->payment_status === 'Paid') {
            return array('success' => false, 'message' => 'Order is already marked as paid');
        }
        if ($this->order->order_status === 'Cancelled') {
            return array('success' => false, 'message' => 'Cancelled orders cannot be marked as paid');
        }

        $this->order->payment_status = 'Paid';
        if ($this->order->update()) {
            return array('success' => true, 'message' => 'Order #' . (int) $id . ' marked as paid');
        }

        return array('success' => false, 'message' => 'Failed to update payment status');
    }

    // --- Customer-facing actions (always scoped to the logged-in user) ---

    public function placeCustomerOrder($userId, $data)
    {
        $paymentType = $data['payment_type'] ?? '';
        if (!in_array($paymentType, self::CUSTOMER_PAYMENT_TYPES, true)) {
            return array('success' => false, 'message' => 'Please choose a payment method');
        }

        $reference = null;
        if ($paymentType === 'QRPH') {
            $reference = strtoupper(trim($data['reference_number'] ?? ''));
            if (!preg_match('/^[A-Z0-9-]{6,30}$/', $reference)) {
                return array('success' => false, 'message' => 'Enter the reference number from your QR PH payment (6-30 letters or digits)');
            }
        }

        $notes = trim($data['notes'] ?? '');
        if (mb_strlen($notes) > 500) {
            return array('success' => false, 'message' => 'Notes must be 500 characters or fewer');
        }

        $result = $this->store(array(
            'user_id'          => $userId,
            'payment_status'   => 'Pending',
            'payment_type'     => $paymentType,
            'reference_number' => $reference,
            'order_status'     => 'Pending',
            'notes'            => $notes,
            'items'            => $data['items'] ?? array(),
        ));

        if ($result['success']) {
            $payment = $paymentType === 'QRPH' ? "QR PH (ref $reference)" : $paymentType;
            (new NotificationController())->addNotification(
                'New order #' . $result['order_id'],
                'New order for ₱' . number_format($result['total_amount'], 2) . ' paid via ' . $payment . '.',
                'order'
            );
        }

        return $result;
    }

    public function getCustomerOrders($userId)
    {
        $result = $this->order->readByUser($userId);
        $orders = array();

        while ($row = $result->fetch_assoc()) {
            $row['items'] = $this->getOrderItems($row['order_id']);
            $orders[] = $row;
        }

        return $orders;
    }

    public function cancelCustomerOrder($userId, $orderId)
    {
        $check = $this->findEditableCustomerOrder($userId, $orderId);
        if (!$check['success']) {
            return $check;
        }

        $this->order->order_status = 'Cancelled';
        if ($this->order->update()) {
            (new NotificationController())->addNotification(
                'Order #' . $orderId . ' cancelled',
                'The customer cancelled order #' . $orderId . '.',
                'order'
            );
            return array('success' => true, 'message' => 'Order cancelled');
        }

        return array('success' => false, 'message' => 'Failed to cancel order');
    }

    /**
     * $quantities: [order_item_id => quantity]; 0 removes the item.
     * Prices stay as they were when the order was placed.
     */
    public function updateCustomerOrder($userId, $orderId, array $quantities, $notes)
    {
        $check = $this->findEditableCustomerOrder($userId, $orderId);
        if (!$check['success']) {
            return $check;
        }

        $notes = trim((string)$notes);
        if (mb_strlen($notes) > 500) {
            return array('success' => false, 'message' => 'Notes must be 500 characters or fewer');
        }

        $items = $this->getOrderItems($orderId);
        $total = 0;
        $kept  = 0;

        foreach ($items as $item) {
            $id  = $item['order_item_id'];
            $qty = array_key_exists($id, $quantities) ? (int)$quantities[$id] : (int)$item['quantity'];
            if ($qty < 0 || $qty > 99) {
                return array('success' => false, 'message' => 'Invalid quantity');
            }
            if ($qty > 0) {
                $kept++;
                $total += $qty * (float)$item['unit_price'];
            }
        }

        if ($kept === 0) {
            return array('success' => false, 'message' => 'An order needs at least one item. Cancel the order instead.');
        }

        foreach ($items as $item) {
            $id  = $item['order_item_id'];
            $qty = array_key_exists($id, $quantities) ? (int)$quantities[$id] : (int)$item['quantity'];
            if ($qty === 0) {
                $this->orderItem->order_item_id = $id;
                $this->orderItem->delete();
            } elseif ($qty !== (int)$item['quantity']) {
                $this->orderItem->updateQuantity($id, $qty);
            }
        }

        $this->order->total_amount = $total;
        $this->order->notes        = $notes;

        if ($this->order->update()) {
            return array('success' => true, 'message' => 'Order updated', 'total_amount' => $total);
        }

        return array('success' => false, 'message' => 'Failed to update order');
    }

    /** Loads the order into $this->order if it belongs to the user and is still Pending. */
    private function findEditableCustomerOrder($userId, $orderId)
    {
        $this->order->order_id     = (int)$orderId;
        $this->order->order_status = null;
        $this->order->readOne();

        if (empty($this->order->order_status) || (int)$this->order->user_id !== (int)$userId) {
            return array('success' => false, 'message' => 'Order not found');
        }

        if ($this->order->order_status !== 'Pending') {
            return array('success' => false, 'message' => 'Only pending orders can be changed. This order is ' . $this->order->order_status . '.');
        }

        return array('success' => true);
    }

    private function getOrderItems($orderId)
    {
        $result = $this->orderItem->readByOrderId($orderId);
        $items  = array();

        while ($row = $result->fetch_assoc()) {
            // Get product details for current name/price reference
            $this->product->product_id = $row['product_id'];
            $this->product->readOne();

            $items[] = array(
                'order_item_id' => $row['order_item_id'],
                'product_id'    => $row['product_id'],
                'product_name'  => $this->product->product_name,
                'variant_type'  => $row['variant_type'],
                'size_label'    => $row['size_label'],
                'quantity'      => $row['quantity'],
                'unit_price'    => $row['unit_price'],
                'subtotal'      => $row['quantity'] * $row['unit_price']
            );
        }

        return $items;
    }

    private function deleteOrderItems($orderId)
    {
        $result = $this->orderItem->readByOrderId($orderId);

        while ($row = $result->fetch_assoc()) {
            $this->orderItem->order_item_id = $row['order_item_id'];
            $this->orderItem->delete();
        }
    }
}
?>