<?php
/**
 * Shared customer footer. Expects $base (path from the current page to public/).
 */
?>
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
                    <a href="<?= $base ?>index.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">Home</a>
                    <a href="<?= $base ?>page/index.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">Menu</a>
                    <a href="<?= $base ?>page/about.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">About</a>
                    <a href="<?= $base ?>page/contact.php" class="text-decoration-none" style="color: #A1887F; cursor: pointer;">Contact</a>
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
