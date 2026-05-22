<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .brand-logo { font-family: 'Playfair Display', serif; font-weight: 800; }
        .nav-link { font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.02em; }
        .hero-title { font-family: 'Playfair Display', serif; font-weight: 700; }
        .section-title { font-family: 'Playfair Display', serif; font-weight: 700; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>
    <header style="background-color: #3D2B1F;">
        <div class="container-fluid py-4 px-4">
            <div class="d-flex align-items-center justify-content-between" style="padding: 0 5rem;">
                <span class="fs-3 brand-logo" style="color: #fff;">Daily Spot Cafe</span>
                <div class="d-flex align-items-center gap-5">
                    <span class="nav-link" style="color: #fff; cursor: pointer;">Home</span>
                    <span class="nav-link" style="color: #fff; cursor: pointer;">Menu</span>
                    <span class="nav-link" style="color: #fff; cursor: pointer;">About</span>
                    <span class="nav-link" style="color: #fff; cursor: pointer;">Contact</span>
                </div>
                <div class="d-flex align-items-center gap-4 flex-shrink-0">
                    <i class="bi bi-cart3 fs-4" style="color: #fff; cursor: pointer;"></i>
                    <i class="bi bi-person-circle fs-4" style="color: #fff; cursor: pointer;"></i>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5" style="background-color: #F2E8DF;">
        <div class="container text-center py-5">
            <h1 class="hero-title display-3 mb-3" style="color: #3D2B1F;">Welcome to Daily Spot Cafe</h1>
            <p class="fs-5 mb-4" style="color: #5D4037; max-width: 600px; margin: 0 auto;">Your cozy neighborhood spot for handcrafted coffee, fresh pastries, and warm conversations.</p>
            <a href="#" class="btn btn-lg px-5 py-3" style="background-color: #3D2B1F; color: #fff; border-radius: 0;">View Our Menu</a>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <h2 class="section-title text-center mb-5" style="color: #3D2B1F;">Our Specialties</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-cup-hot fs-1 mb-3" style="color: #D35400;"></i>
                            <h5 class="card-title" style="color: #3D2B1F;">Artisan Coffee</h5>
                            <p class="card-text" style="color: #5D4037;">Single-origin beans roasted to perfection, brewed just for you.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-cake fs-1 mb-3" style="color: #D35400;"></i>
                            <h5 class="card-title" style="color: #3D2B1F;">Fresh Pastries</h5>
                            <p class="card-text" style="color: #5D4037;">Baked daily with love — from flaky croissants to decadent cakes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
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

    <section class="py-5" style="background-color: #E8D5C4;">
        <div class="container text-center py-4">
            <h2 class="section-title mb-4" style="color: #3D2B1F;">Visit Us Today</h2>
            <p class="fs-5 mb-4" style="color: #5D4037; max-width: 500px; margin: 0 auto;">Open daily from 7:00 AM to 9:00 PM. Come find your new favorite spot.</p>
            <div class="d-flex justify-content-center gap-3">
                <i class="bi bi-geo-alt fs-4" style="color: #3D2B1F;"></i>
                <span style="color: #5D4037;">123 Cafe Street, Manila</span>
                <i class="bi bi-telephone fs-4" style="color: #3D2B1F;"></i>
                <span style="color: #5D4037;">(02) 1234 5678</span>
            </div>
        </div>
    </section>

    <footer style="background-color: #3D2B1F;">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="brand-logo fs-4 d-block mb-3" style="color: #fff;">Daily Spot Cafe</span>
                    <p style="color: #A1887F;">Your cozy neighborhood spot for handcrafted coffee, fresh pastries, and warm conversations. Come find your new favorite place.</p>
                    <div class="d-flex gap-3">
                        <i class="bi bi-facebook fs-5" style="color: #A1887F; cursor: pointer;"></i>
                        <i class="bi bi-instagram fs-5" style="color: #A1887F; cursor: pointer;"></i>
                        <i class="bi bi-twitter-x fs-5" style="color: #A1887F; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3" style="color: #fff;">Quick Links</h6>
                    <div class="d-flex flex-column gap-2">
                        <span style="color: #A1887F; cursor: pointer;">Home</span>
                        <span style="color: #A1887F; cursor: pointer;">Menu</span>
                        <span style="color: #A1887F; cursor: pointer;">About</span>
                        <span style="color: #A1887F; cursor: pointer;">Contact</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3" style="color: #fff;">Opening Hours</h6>
                    <div class="d-flex flex-column gap-2">
                        <span style="color: #A1887F;">Mon - Fri: 7:00 AM - 9:00 PM</span>
                        <span style="color: #A1887F;">Saturday: 8:00 AM - 10:00 PM</span>
                        <span style="color: #A1887F;">Sunday: 8:00 AM - 8:00 PM</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3" style="color: #fff;">Contact</h6>
                    <div class="d-flex flex-column gap-2">
                        <span style="color: #A1887F;"><i class="bi bi-geo-alt me-2"></i>123 Cafe Street, Manila</span>
                        <span style="color: #A1887F;"><i class="bi bi-telephone me-2"></i>(02) 1234 5678</span>
                        <span style="color: #A1887F;"><i class="bi bi-envelope me-2"></i>hello@dailyspotcafe.com</span>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-3" style="border-color: #5D4037;">
            <div class="text-center">
                <span style="color: #A1887F;">&copy; 2026 Daily Spot Cafe. All rights reserved.</span>
            </div>
        </div>
    </footer>
</body>
</html>
