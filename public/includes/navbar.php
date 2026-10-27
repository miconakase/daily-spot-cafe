<?php
/**
 * Shared customer navbar.
 * Expects: $base   - path from the current page to public/ ('' or '../')
 *          $active - 'home' | 'menu' | 'about' | 'contact' | ''
 *          $loginRedirect (optional) - where to return after logging in
 */
$navLoggedIn = isset($_SESSION['user_id']);
$navGreeting = $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';
$active = $active ?? '';
$loginRedirect = $loginRedirect ?? '../page/index.php';

$navLinks = [
    'home'    => ['Home', $base . 'index.php'],
    'menu'    => ['Menu', $base . 'page/index.php'],
    'about'   => ['About', $base . 'page/about.php'],
    'contact' => ['Contact', $base . 'page/contact.php'],
];
?>
<header class="site-header" style="background-color: #3D2B1F;">
        <div class="site-nav">
            <a href="<?= $base ?>index.php" class="site-brand brand-logo text-decoration-none" style="color: #fff;">Daily Spot Cafe</a>
            <!-- Inline on desktop; a drop-down panel under the bar on phones (toggled in nav.js) -->
            <nav class="site-links" id="siteLinks" aria-label="Main">
                <?php foreach ($navLinks as $key => [$label, $href]): ?>
                    <a href="<?= $href ?>" class="nav-link text-decoration-none" style="color: <?= $active === $key ? '#D35400' : '#fff' ?>; cursor: pointer;"<?= $active === $key ? ' aria-current="page"' : '' ?>><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="site-actions d-flex align-items-center flex-shrink-0">
                <div class="cart-container" data-checkout-url="<?= $base ?>page/checkout.php">
                    <i class="bi bi-cart3 fs-4" style="color: #fff; cursor: pointer;" id="cartIcon"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </div>
                <div class="user-container">
                    <i class="bi bi-person-circle fs-4" style="color: #fff; cursor: pointer;" id="userIcon"></i>
                    <div class="user-dropdown" id="userDropdown">
                        <?php if ($navLoggedIn): ?>
                            <div class="user-greeting">Hi, <?= htmlspecialchars($navGreeting) ?></div>
                            <div class="user-dropdown-divider"></div>
                            <a href="<?= $base ?>page/profile.php" class="user-dropdown-item"><i class="bi bi-person"></i>My Profile</a>
                            <a href="<?= $base ?>page/orders.php" class="user-dropdown-item"><i class="bi bi-receipt"></i>My Orders</a>
                            <div class="user-dropdown-divider"></div>
                            <a href="<?= $base ?>auth/logout.php" class="user-dropdown-item"><i class="bi bi-box-arrow-right"></i>Logout</a>
                        <?php else: ?>
                            <a href="<?= $base ?>auth/login.php?redirect=<?= urlencode($loginRedirect) ?>" class="user-dropdown-item"><i class="bi bi-box-arrow-in-right"></i>Login</a>
                            <a href="<?= $base ?>auth/signup.php?redirect=<?= urlencode($loginRedirect) ?>" class="user-dropdown-item"><i class="bi bi-person-plus"></i>Sign Up</a>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="button" class="nav-toggle" id="navToggle" aria-controls="siteLinks" aria-expanded="false" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
</header>
