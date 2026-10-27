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
<header style="background-color: #3D2B1F;">
    <div class="container-fluid py-4 px-4">
        <div class="d-flex align-items-center justify-content-between" style="padding: 0 5rem;">
            <a href="<?= $base ?>index.php" class="fs-3 brand-logo text-decoration-none" style="color: #fff;">Daily Spot Cafe</a>
            <div class="d-flex align-items-center gap-5">
                <?php foreach ($navLinks as $key => [$label, $href]): ?>
                    <a href="<?= $href ?>" class="nav-link text-decoration-none" style="color: <?= $active === $key ? '#D35400' : '#fff' ?>; cursor: pointer;"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
            <div class="d-flex align-items-center gap-4 flex-shrink-0">
                <div class="cart-container" style="position: relative;" data-checkout-url="<?= $base ?>page/checkout.php">
                    <i class="bi bi-cart3 fs-4" style="color: #fff; cursor: pointer;" id="cartIcon"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </div>
                <div class="user-container" style="position: relative;">
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
            </div>
        </div>
    </div>
</header>
