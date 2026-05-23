<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu - Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .brand-logo { font-family: 'Playfair Display', serif; font-weight: 800; }
        .nav-link { font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.02em; transition: color 0.2s; }
        .nav-link:hover { color: #D35400 !important; }
        .section-title { font-family: 'Playfair Display', serif; font-weight: 700; }
        .product-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .category-pill { cursor: pointer; transition: all 0.2s; border: 2px solid #3D2B1F; color: #3D2B1F; background: transparent; border-radius: 50px; padding: 8px 24px; font-weight: 500; }
        .category-pill:hover { background: #3D2B1F; color: #fff; }
        .category-pill.active { background: #3D2B1F; color: #fff; }
        body { font-family: 'Inter', sans-serif; background: #FDF8F4; }
        .product-img { width: 100%; height: 320px; object-fit: cover; }
    </style>
</head>
<body>
    <header style="background-color: #3D2B1F;">
        <div class="container-fluid py-4 px-4">
            <div class="d-flex align-items-center justify-content-between" style="padding: 0 5rem;">
                <a href="../index.php" class="fs-3 brand-logo text-decoration-none" style="color: #fff;">Daily Spot Cafe</a>
                <div class="d-flex align-items-center gap-5">
                    <a href="../index.php" class="nav-link text-decoration-none" style="color: #fff; cursor: pointer;">Home</a>
                    <a href="index.php" class="nav-link text-decoration-none" style="color: #D35400; cursor: pointer;">Menu</a>
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

    <section class="py-5">
        <div class="container py-4">
            <h1 class="section-title text-center mb-2" style="color: #3D2B1F;">Our Menu</h1>
            <p class="text-center mb-5" style="color: #5D4037;">Explore our selection of handcrafted drinks</p>

            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5" id="categoryFilters">
            </div>

            <div class="row g-4" id="productGrid">
            </div>

            <div class="text-center py-5" id="noProducts" style="display: none;">
                <i class="bi bi-cup-hot fs-1" style="color: #A1887F;"></i>
                <p class="mt-3" style="color: #5D4037;">No items found in this category.</p>
            </div>
        </div>
    </section>

    <footer style="background-color: #3D2B1F;">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="brand-logo fs-4 d-block mb-3" style="color: #fff;">Daily Spot Cafe</span>
                    <p style="color: #A1887F;">Your cozy neighborhood spot for handcrafted coffee, refreshing drinks, and warm conversations. Come find your new favorite place.</p>
                    <div class="d-flex gap-3">
                        <i class="bi bi-facebook fs-5" style="color: #A1887F; cursor: pointer;"></i>
                        <i class="bi bi-instagram fs-5" style="color: #A1887F; cursor: pointer;"></i>
                        <i class="bi bi-twitter-x fs-5" style="color: #A1887F; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3" style="color: #fff;">Quick Links</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="../index.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">Home</a>
                        <a href="index.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">Menu</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const categories = <?php
            require_once '../../src/controller/CategoryController.php';
            $catCtrl = new CategoryController();
            echo json_encode($catCtrl->getActiveCategories());
        ?>;

        const allProducts = <?php
            require_once '../../src/controller/ProductController.php';
            $prodCtrl = new ProductController();
            echo json_encode($prodCtrl->getPublishedProducts());
        ?>;

        const filters = document.getElementById('categoryFilters');
        const grid = document.getElementById('productGrid');
        const noProducts = document.getElementById('noProducts');

        function renderProducts(categoryId) {
            const filtered = categoryId === null
                ? allProducts
                : allProducts.filter(p => p.category_id == categoryId);

            grid.innerHTML = '';
            if (filtered.length === 0) {
                noProducts.style.display = 'block';
                return;
            }
            noProducts.style.display = 'none';

            filtered.forEach(p => {
                const imgSrc = p.img_url ? '../' + p.img_url : 'https://placehold.co/400x300/E8D5C4/3D2B1F?text=No+Image';
                const price = '₱' + parseFloat(p.price).toFixed(2);
                const desc = p.short_description || '';

                grid.innerHTML += `
                    <div class="col-md-6 col-lg-4">
                        <a href="product.php?id=${p.product_id}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm product-card">
                                <img src="${imgSrc}" class="card-img-top product-img" alt="${p.product_name}">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title fw-bold mb-1" style="color: #3D2B1F;">${p.product_name}</h6>
                                    <p class="card-text small mb-2" style="color: #5D4037;">${desc}</p>
                                    <div class="mb-2">
                                        <i class="bi bi-star-fill" style="color: #FFB800; font-size: 0.85rem;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFB800; font-size: 0.85rem;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFB800; font-size: 0.85rem;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFB800; font-size: 0.85rem;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFB800; font-size: 0.85rem;"></i>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-auto">
                                        <span class="fw-bold" style="color: #D35400; font-size: 1.1rem;">${price}</span>
                                        <button class="btn btn-sm" style="background-color: #3D2B1F; color: #fff; border-radius: 0;">
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });
        }

        function buildFilters() {
            filters.innerHTML = '<button class="category-pill active" data-id="all">All</button>';
            categories.forEach(c => {
                filters.innerHTML += `<button class="category-pill" data-id="${c.category_id}">${c.category_name}</button>`;
            });

            filters.querySelectorAll('.category-pill').forEach(btn => {
                btn.addEventListener('click', function() {
                    filters.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const id = this.dataset.id === 'all' ? null : parseInt(this.dataset.id);
                    renderProducts(id);
                });
            });
        }

        buildFilters();
        renderProducts(null);
    </script>
</body>
</html>
