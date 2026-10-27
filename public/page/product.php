<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$greeting = $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';

require_once '../../src/controller/ProductController.php';
$ctrl = new ProductController();
$product = $ctrl->show($_GET['id'] ?? 0);
if (!$product) {
    header('Location: index.php');
    exit;
}

// Rich text from the admin editor is cleaned again on output; plain text keeps its line breaks
require_once '../../src/helpers/HtmlSanitizer.php';
$description = (string) $product['long_description'];
if (HtmlSanitizer::isBlank($description)) {
    $descriptionHtml = nl2br(htmlspecialchars($product['short_description']));
} elseif (strpos($description, '<') !== false) {
    $descriptionHtml = HtmlSanitizer::clean($description);
} else {
    $descriptionHtml = nl2br(htmlspecialchars($description));
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($product['product_name']) ?> - Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .brand-logo { font-family: 'Playfair Display', serif; font-weight: 800; }
        .nav-link { font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.02em; transition: color 0.2s; }
        .nav-link:hover { color: #D35400 !important; }
        .section-title { font-family: 'Playfair Display', serif; font-weight: 700; }
        body { font-family: 'Inter', sans-serif; background: #FDF8F4; }
        .variant-btn { border: 2px solid #3D2B1F; color: #3D2B1F; background: transparent; border-radius: 0; padding: 8px 24px; font-weight: 500; transition: all 0.2s; cursor: pointer; }
        .variant-btn:hover, .variant-btn.active { background: #3D2B1F; color: #fff; }
        .cart-badge { position: absolute; top: -8px; right: -8px; background: #D35400; color: #fff; font-size: 0.6rem; font-weight: 700; width: 18px; height: 18px; border-radius: 50%; display: none; align-items: center; justify-content: center; }
        .cart-dropdown { position: absolute; top: 100%; right: 0; z-index: 1050; background: #fff; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); width: 340px; display: none; margin-top: 10px; max-height: 480px; flex-direction: column; }
        .cart-dropdown-header { padding: 16px 16px 12px; border-bottom: 1px solid #f0f0f0; }
        .cart-dropdown-header h6 { margin: 0; color: #3D2B1F; font-weight: 700; }
        .cart-dropdown-body { flex: 1; overflow-y: auto; padding: 8px 0; min-height: 100px; max-height: 300px; }
        .cart-empty-state { text-align: center; padding: 30px 16px; color: #A1887F; }
        .cart-empty-state i { font-size: 2.5rem; display: block; margin-bottom: 8px; }
        .cart-empty-state p { margin: 0; font-size: 0.9rem; }
        .cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-bottom: 1px solid #f5f5f5; }
        .cart-item-img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-name { font-size: 0.85rem; font-weight: 600; color: #3D2B1F; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cart-item-variant { font-size: 0.75rem; color: #A1887F; }
        .cart-item-price { font-size: 0.8rem; color: #D35400; font-weight: 600; }
        .cart-item-qty { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
        .qty-btn { width: 24px; height: 24px; border: 1px solid #ddd; background: #fff; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; cursor: pointer; color: #5D4037; line-height: 1; padding: 0; }
        .qty-btn:hover { background: #f5f5f5; }
        .cart-item-qty span { font-size: 0.85rem; font-weight: 600; color: #3D2B1F; min-width: 20px; text-align: center; }
        .cart-item-remove { background: none; border: none; color: #ccc; cursor: pointer; font-size: 1.1rem; padding: 4px; line-height: 1; flex-shrink: 0; }
        .cart-item-remove:hover { color: #e74c3c; }
        .cart-dropdown-footer { padding: 12px 16px 16px; border-top: 1px solid #f0f0f0; }
        .cart-total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .cart-total-row span { font-size: 0.9rem; color: #5D4037; }
        .cart-total-amount { font-weight: 700; color: #3D2B1F; font-size: 1rem; }
        .btn-checkout { width: 100%; background-color: #3D2B1F; color: #fff; border: none; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:hover { background-color: #2c1f14; }
        .btn-checkout:disabled { background-color: #ccc; cursor: not-allowed; }
        .user-dropdown { position: absolute; top: 100%; right: 0; z-index: 1050; background: #fff; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); width: 200px; display: none; margin-top: 10px; overflow: hidden; }
        .user-greeting { padding: 14px 16px 8px; font-size: 0.9rem; font-weight: 600; color: #3D2B1F; }
        .user-dropdown-divider { height: 1px; background: #f0f0f0; margin: 4px 0; }
        .user-dropdown-item { display: block; padding: 10px 16px; color: #5D4037; text-decoration: none; font-size: 0.9rem; transition: background 0.15s; cursor: pointer; }
        .user-dropdown-item:hover { background: #FDF8F4; color: #3D2B1F; }
        .user-dropdown-item i { margin-right: 8px; width: 16px; }
        .product-description p:last-child, .product-description ul:last-child, .product-description ol:last-child { margin-bottom: 0; }
        .product-description a { color: #D35400; }
    </style>
</head>
<body>
    <?php $base = '../'; $active = 'menu'; $loginRedirect = '../page/product.php?id=' . $product['product_id']; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5">
        <div class="container py-4">
            <a href="index.php" class="text-decoration-none mb-4 d-inline-block" style="color: #5D4037;">
                <i class="bi bi-arrow-left me-1"></i>Back to Menu
            </a>

            <div class="row g-5">
                <div class="col-md-6">
                    <div class="rounded overflow-hidden">
                        <?php if ($product['img_url']): ?>
                            <img src="../<?= htmlspecialchars($product['img_url']) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($product['product_name']) ?>" style="height: 450px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 450px;">
                                <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <span class="badge mb-2" style="background-color: #E8D5C4; color: #3D2B1F; font-weight: 500;"><?= htmlspecialchars($product['category_name']) ?></span>
                    <h1 class="section-title display-5 mb-2" style="color: #3D2B1F;"><?= htmlspecialchars($product['product_name']) ?></h1>
                    <div class="mb-3">
                        <i class="bi bi-star-fill" style="color: #FFB800;"></i>
                        <i class="bi bi-star-fill" style="color: #FFB800;"></i>
                        <i class="bi bi-star-fill" style="color: #FFB800;"></i>
                        <i class="bi bi-star-fill" style="color: #FFB800;"></i>
                        <i class="bi bi-star-fill" style="color: #FFB800;"></i>
                        <span class="ms-2" style="color: #5D4037;">5.0</span>
                    </div>

                    <?php if (!empty($product['variants'])): ?>
                        <?php if ($product['has_variants']): ?>
                            <div class="mb-3">
                                <label class="fw-bold small mb-2" style="color: #3D2B1F;">Variant</label>
                                <div class="d-flex gap-2" id="variantTypeGroup">
                                    <button class="variant-btn active" data-value="Hot">Hot</button>
                                    <button class="variant-btn" data-value="Iced">Iced</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['has_sizes']): ?>
                            <div class="mb-3">
                                <label class="fw-bold small mb-2" style="color: #3D2B1F;">Size</label>
                                <div class="d-flex gap-2" id="sizeGroup">
                                    <button class="variant-btn active" data-value="Grande">Grande</button>
                                    <button class="variant-btn" data-value="Venti">Venti</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mb-4">
                            <span class="fw-bold" style="color: #D35400; font-size: 1.8rem;" id="detailPrice">₱<?= number_format($product['variants'][0]['price'], 2) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <span class="fw-bold" style="color: #D35400; font-size: 1.8rem;">₱<?= number_format($product['price'], 2) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="product-description mb-4" style="color: #5D4037; line-height: 1.8;"><?= $descriptionHtml ?></div>

                    <button class="btn btn-lg px-5 py-3" id="addToCartBtn" style="background-color: #3D2B1F; color: #fff; border-radius: 0;">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!empty($product['variants'])): ?>
        const variants = <?= json_encode($product['variants']) ?>;
        const hasVariants = <?= $product['has_variants'] ? 'true' : 'false' ?>;
        const hasSizes = <?= $product['has_sizes'] ? 'true' : 'false' ?>;
        const priceEl = document.getElementById('detailPrice');

        function updatePrice() {
            const selVariant = hasVariants ? document.querySelector('#variantTypeGroup .active')?.dataset.value : null;
            const selSize = hasSizes ? document.querySelector('#sizeGroup .active')?.dataset.value : null;
            const match = variants.find(v =>
                (v.variant_type || null) === (selVariant || null) &&
                (v.size_label || null) === (selSize || null)
            );
            if (match) {
                priceEl.textContent = '₱' + parseFloat(match.price).toFixed(2);
            }
        }

        document.querySelectorAll('#variantTypeGroup .variant-btn, #sizeGroup .variant-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.parentElement.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                updatePrice();
            });
        });
        <?php endif; ?>

        const productId = <?= $product['product_id'] ?>;
        const productName = <?= json_encode($product['product_name']) ?>;
        const productPrice = <?= json_encode($product['price']) ?>;
        const productImg = <?= json_encode($product['img_url'] ? '../' . $product['img_url'] : '') ?>;

        document.getElementById('addToCartBtn')?.addEventListener('click', function() {
            let price = parseFloat(productPrice);
            let variantType = null;
            let sizeLabel = null;

            <?php if (!empty($product['variants'])): ?>
            const selVariant = hasVariants ? document.querySelector('#variantTypeGroup .active')?.dataset.value : null;
            const selSize = hasSizes ? document.querySelector('#sizeGroup .active')?.dataset.value : null;
            const match = variants.find(v =>
                (v.variant_type || null) === (selVariant || null) &&
                (v.size_label || null) === (selSize || null)
            );
            if (match) {
                price = parseFloat(match.price);
                variantType = selVariant;
                sizeLabel = selSize;
            }
            <?php endif; ?>

            CartManager.addItem({
                product_id: productId,
                product_name: productName,
                price: price,
                img_url: productImg,
                variant_type: variantType,
                size_label: sizeLabel
            });

            const dd = document.getElementById('cartDropdown');
            if (dd) dd.style.display = 'block';
        });
    </script>
    <script src="../assets/javascript/cart.js"></script>
    <script src="../assets/javascript/nav.js"></script>
</body>
</html>
