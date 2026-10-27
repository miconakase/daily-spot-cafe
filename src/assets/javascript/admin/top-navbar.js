document.addEventListener('DOMContentLoaded', function() {
    // Each toggle opens its dropdown and closes the others
    const pairs = [
        ['messageToggle', 'messageDropdown'],
        ['notificationToggle', 'notificationDropdown'],
        ['profileToggle', 'profileDropdown'],
    ]
        .map(([toggleId, dropdownId]) => [document.getElementById(toggleId), document.getElementById(dropdownId)])
        .filter(([toggle, dropdown]) => toggle && dropdown);

    function closeAll(except) {
        pairs.forEach(([, dropdown]) => {
            if (dropdown !== except) dropdown.classList.remove('show');
        });
    }

    pairs.forEach(([toggle, dropdown]) => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            closeAll(dropdown);
            dropdown.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        pairs.forEach(([toggle, dropdown]) => {
            if (dropdown.classList.contains('show') && !dropdown.contains(e.target) && !toggle.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });

    // Close dropdowns when pressing Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAll(null);
    });
});
