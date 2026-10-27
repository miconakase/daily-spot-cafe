<?php
// Application settings.

// 'development' shows password reset links on screen because there is no mail
// server locally. Set to 'production' once real email sending is configured.
define('APP_ENV', 'development');

// Public base URL used in emailed links, e.g. 'https://dailyspotcafe.com/daily-spot-cafe'.
// Leave empty in development to use the current host. Set it in production so
// a forged Host header can't redirect reset links.
define('APP_URL', '');

// Login lockout (see src/model/LoginAttempt.php)
define('LOGIN_MAX_ATTEMPTS', 3);
define('LOGIN_LOCKOUT_SECONDS', 60);

// Password reset links stay valid for this many minutes
define('PASSWORD_RESET_TTL_MINUTES', 30);

// Shown in the QR PH payment modal. Drop your QR image at this web path
// (relative to public/) to replace the placeholder.
define('QRPH_IMAGE', 'assets/images/qrph.png');
define('QRPH_ACCOUNT_NAME', 'Daily Spot Cafe');
?>
