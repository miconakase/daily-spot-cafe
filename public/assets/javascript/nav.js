// User menu toggle for the shared navbar (public/includes/navbar.php)
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');
    if (!userIcon || !userDropdown) return;

    userIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = userDropdown.style.display === 'block';
        const cartDropdown = document.getElementById('cartDropdown');
        if (cartDropdown) cartDropdown.style.display = 'none';
        userDropdown.style.display = isVisible ? 'none' : 'block';
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-container')) {
            userDropdown.style.display = 'none';
        }
    });
});
