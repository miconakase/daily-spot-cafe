<?php
require_once __DIR__ . '/../model/Message.php';
require_once __DIR__ . '/NotificationController.php';

class MessageController
{
    private $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    public function index($limit = null)
    {
        $result = $this->message->read($limit);
        $messages = array();

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        return $messages;
    }

    /** Returns the message and marks it read (opening it counts as reading it). */
    public function open($id)
    {
        $message = $this->message->readOne((int) $id);
        if (!$message) {
            return array('success' => false, 'message' => 'Message not found');
        }

        if (!$message['is_read']) {
            $this->message->markAsRead((int) $id);
            $message['is_read'] = 1;
        }

        return array('success' => true, 'data' => $message);
    }

    public function getUnreadCount()
    {
        return $this->message->unreadCount();
    }

    public function markAllAsRead()
    {
        $this->message->markAllAsRead();
        return array('success' => true, 'message' => 'All messages marked as read');
    }

    public function destroy($id)
    {
        if ($this->message->delete((int) $id)) {
            return array('success' => true, 'message' => 'Message deleted');
        }
        return array('success' => false, 'message' => 'Failed to delete message');
    }

    /** Saves a contact-form message and notifies the admin. */
    public function store($data, $userId = null)
    {
        $name    = trim(strip_tags($data['name'] ?? ''));
        $email   = trim($data['email'] ?? '');
        $subject = trim(strip_tags($data['subject'] ?? ''));
        $body    = trim(strip_tags($data['message'] ?? ''));

        if ($name === '' || mb_strlen($name) > 150) {
            return array('success' => false, 'message' => 'Please enter your name.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('success' => false, 'message' => 'Please enter a valid email address.');
        }
        if ($subject === '' || mb_strlen($subject) > 200) {
            return array('success' => false, 'message' => 'Please enter a subject (up to 200 characters).');
        }
        if (mb_strlen($body) < 10 || mb_strlen($body) > 2000) {
            return array('success' => false, 'message' => 'Your message should be 10 to 2000 characters.');
        }

        $this->message->user_id = $userId;
        $this->message->name    = $name;
        $this->message->email   = $email;
        $this->message->subject = $subject;
        $this->message->message = $body;

        if ($this->message->create()) {
            (new NotificationController())->addNotification(
                'New message from ' . $name,
                $subject,
                'message'
            );
            return array('success' => true, 'message' => 'Thanks! Your message has been sent. We will get back to you soon.');
        }

        return array('success' => false, 'message' => 'Failed to send your message. Please try again.');
    }
}
?>
