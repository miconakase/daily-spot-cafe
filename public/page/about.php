<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$greeting = $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';
require_once __DIR__ . '/../../src/config/app.php';
$store = array_map('htmlspecialchars', STORE);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About - Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .brand-logo { font-family: 'Playfair Display', serif; font-weight: 800; }
        .nav-link { font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.02em; transition: color 0.2s; }
        .nav-link:hover { color: #D35400 !important; }
        .section-title { font-family: 'Playfair Display', serif; font-weight: 700; }
        body { font-family: 'Inter', sans-serif; background: #FDF8F4; }
        .about-card { transition: transform 0.2s, box-shadow 0.2s; }
        .about-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
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
    </style>
</head>
<body>
    <?php $base = '../'; $active = 'about'; $loginRedirect = '../page/about.php'; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5 position-relative" style="background: url('../uploads/DSP_Hero.jpg') center center / cover no-repeat; min-height: 50vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0,0,0,0.55);"></div>
        <div class="container text-center py-5 position-relative">
            <h1 class="section-title display-3 mb-3" style="color: #fff; text-shadow: 2px 2px 8px rgba(0,0,0,0.7);">About Daily Spot Cafe</h1>
            <p class="fs-5 mb-0" style="color: #fff; text-shadow: 1px 1px 6px rgba(0,0,0,0.7); max-width: 600px; margin: 0 auto;">Your cozy neighborhood spot for handcrafted coffee, refreshing drinks, and warm conversations.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title mb-4" style="color: #3D2B1F;">Our Story</h2>
                    <p style="color: #5D4037; line-height: 1.8;">The Daily Spot Cafe was born from a simple love for great coffee and genuine connection. Founded in the heart of Manila, we set out to create a space where every cup tells a story and every guest feels at home.</p>
                    <p style="color: #5D4037; line-height: 1.8;">From our carefully sourced single-origin beans to our handcrafted beverages made fresh every day, we believe in quality that you can taste. Whether you are stopping by for your morning espresso, catching up with friends over a frappe, or finding a quiet corner to work, the Daily Spot is your place.</p>
                </div>
                <div class="col-lg-6">
                    <div class="rounded overflow-hidden shadow-sm">
                        <div class="d-flex align-items-center justify-content-center" style="background: #E8D5C4; min-height: 350px;">
                            <i class="bi bi-cup-hot" style="font-size: 5rem; color: #3D2B1F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: #fff;">
        <div class="container py-4">
            <h2 class="section-title text-center mb-5" style="color: #3D2B1F;">Our Specialties</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm about-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-cup-hot fs-1 mb-3" style="color: #D35400;"></i>
                            <h5 class="card-title" style="color: #3D2B1F;">Artisan Coffee</h5>
                            <p class="card-text" style="color: #5D4037;">Single-origin beans roasted to perfection, brewed just for you.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm about-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-cup-straw fs-1 mb-3" style="color: #D35400;"></i>
                            <h5 class="card-title" style="color: #3D2B1F;">Non-Coffee Drinks</h5>
                            <p class="card-text" style="color: #5D4037;">Refreshing teas, cool sodas, and creamy frappes for every taste.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm about-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-shop fs-1 mb-3" style="color: #D35400;"></i>
                            <h5 class="card-title" style="color: #3D2B1F;">Cozy Ambiance</h5>
                            <p class="card-text" style="color: #5D4037;">The perfect spot to unwind, work, or catch up with friends.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <h2 class="section-title text-center mb-5" style="color: #3D2B1F;">Visit Us</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-geo-alt fs-1 mb-3" style="color: #D35400;"></i>
                        <h6 class="fw-bold" style="color: #3D2B1F;">Address</h6>
                        <p class="mb-0" style="color: #5D4037;"><?= $store['address'] ?><br><small><?= $store['landmark'] ?></small></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-clock fs-1 mb-3" style="color: #D35400;"></i>
                        <h6 class="fw-bold" style="color: #3D2B1F;">Opening Hours</h6>
                        <p class="mb-0" style="color: #5D4037;"><?= $store['days'] ?><br><?= $store['hours'] ?><br><small><?= $store['closed'] ?></small></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-envelope fs-1 mb-3" style="color: #D35400;"></i>
                        <h6 class="fw-bold" style="color: #3D2B1F;">Contact</h6>
                        <p class="mb-0" style="color: #5D4037;"><a href="tel:<?= $store['phone'] ?>" class="text-decoration-none" style="color: #5D4037;"><?= $store['phone'] ?></a><br><a href="mailto:<?= $store['email'] ?>" class="text-decoration-none" style="color: #5D4037;"><?= $store['email'] ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/javascript/cart.js"></script>
    <script src="../assets/javascript/nav.js"></script>
</body>
</html>
