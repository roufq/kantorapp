document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('mobile-toggle');
    const sidebar = document.querySelector('.side-menu');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Manual Header Dropdowns Toggle
    $('.top-actions .dropdown-toggle').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $target = $(this).next('.dropdown-menu');
        $('.dropdown-menu').not($target).removeClass('show'); // Close others
        $target.toggleClass('show');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
});
