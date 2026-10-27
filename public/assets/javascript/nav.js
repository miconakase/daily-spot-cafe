// Shared navbar (public/includes/navbar.php): user menu and the phone menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');
    const navToggle = document.getElementById('navToggle');
    const siteLinks = document.getElementById('siteLinks');

    function setMenuOpen(open) {
        if (!navToggle || !siteLinks) return;
        siteLinks.classList.toggle('open', open);
        navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        navToggle.querySelector('i').className = open ? 'bi bi-x-lg' : 'bi bi-list';
    }

    if (navToggle && siteLinks) {
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const opening = !siteLinks.classList.contains('open');
            setMenuOpen(opening);
            if (opening) {
                if (userDropdown) userDropdown.style.display = 'none';
                const cartDropdown = document.getElementById('cartDropdown');
                if (cartDropdown) cartDropdown.style.display = 'none';
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') setMenuOpen(false);
        });
    }

    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = userDropdown.style.display === 'block';
            const cartDropdown = document.getElementById('cartDropdown');
            if (cartDropdown) cartDropdown.style.display = 'none';
            setMenuOpen(false);
            userDropdown.style.display = isVisible ? 'none' : 'block';
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-container')) {
                userDropdown.style.display = 'none';
            }
        });
    }

    // Opening the cart closes the phone menu
    const cartIcon = document.getElementById('cartIcon');
    if (cartIcon) cartIcon.addEventListener('click', () => setMenuOpen(false));
});
