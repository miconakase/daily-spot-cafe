<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../model/PasswordReset.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/UserDetails.php';
require_once __DIR__ . '/../model/LoginAttempt.php';

class PasswordResetController
{
    private $reset;
    private $user;
    private $userDetails;

    public function __construct()
    {
        $this->reset       = new PasswordReset();
        $this->user        = new User();
        $this->userDetails = new UserDetails();
    }

    /**
     * Starts a reset for a username or email. The response message is the
     * same whether or not an account matched, so it can't be used to find
     * accounts. In development, 'dev_link' carries the link to show on screen.
     */
    public function requestReset($identifier, $resetPageUrl)
    {
        $identifier = trim($identifier);
        $generic = array('success' => true,
            'message' => 'If an account matches, a password reset link has been sent to its email address.');

        if ($identifier === '') {
            return array('success' => false, 'message' => 'Please enter your username or email.');
        }

        $userId = $this->findUserId($identifier);
        if (!$userId) {
            return $generic;
        }

        $this->userDetails->user_id = $userId;
        $this->userDetails->readOne();
        $email = $this->userDetails->email;

        $token = $this->reset->create($userId, PASSWORD_RESET_TTL_MINUTES);
        if (!$token) {
            return array('success' => false, 'message' => 'Could not start a password reset. Please try again.');
        }

        $link = $resetPageUrl . '?token=' . urlencode($token);

        if (!empty($email)) {
            $body = "We received a request to reset your Daily Spot Cafe password.\n\n"
                  . "Open this link within " . PASSWORD_RESET_TTL_MINUTES . " minutes to choose a new password:\n$link\n\n"
                  . "If you didn't ask for this, you can ignore this email.";
            // No mail server locally; failures are expected in development
            @mail($email, 'Reset your Daily Spot Cafe password', $body);
        }

        if (APP_ENV === 'development') {
            $generic['dev_link'] = $link;
        }

        return $generic;
    }

    public function isValidToken($token)
    {
        return $this->reset->findValidUserId($token) !== null;
    }

    public function resetPassword($token, $password, $confirm)
    {
        $userId = $this->reset->findValidUserId($token);
        if (!$userId) {
            return array('success' => false, 'message' => 'This reset link is invalid or has expired. Please request a new one.');
        }
        if (strlen($password) < 8) {
            return array('success' => false, 'message' => 'Password must be at least 8 characters.');
        }
        if ($password !== $confirm) {
            return array('success' => false, 'message' => 'Passwords do not match.');
        }

        if (!$this->user->updatePassword($userId, password_hash($password, PASSWORD_DEFAULT))) {
            return array('success' => false, 'message' => 'Failed to update password. Please try again.');
        }

        $this->reset->markUsed($token);

        // A successful reset lifts any login lockout
        $this->user->user_id = $userId;
        $this->user->readOne();
        (new LoginAttempt())->clear(strtolower($this->user->username));

        return array('success' => true, 'message' => 'Your password has been reset. You can now log in.');
    }

    private function findUserId($identifier)
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->userDetails->findUserIdByEmail($identifier);
        }

        return $this->user->findIdByUsername($identifier);
    }
}
?>
