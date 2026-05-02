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

    // Dropdowns are handled by Bootstrap 5 natively.
    // Ensure dropdowns don't close when clicking inside (optional)
    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    // Unpoly SPA Configuration
    if (window.up) {
        up.fragment.config.mainTargets.push('.page-body');
        
        // Auto-follow sidebar links and target the page-body
        $('.side-menu a').attr('up-follow', '');
        $('.side-menu a').attr('up-target', '.page-body');

        // Update active menu state after navigation
        up.on('up:fragment:inserted', function() {
            const currentPath = window.location.pathname;
            $('.side-menu a').each(function() {
                const linkPath = new URL($(this).attr('href'), window.location.origin).pathname;
                if (currentPath === linkPath) {
                    $('.side-menu a').removeClass('active');
                    $(this).addClass('active');
                }
            });
        });
    }

    // Sidebar Scroll Memory
    const sidebarScrollKey = 'kantorapp_sidebar_scroll';
    // Record scroll position on every scroll
    if (sidebar) {
        sidebar.addEventListener('scroll', function() {
            sessionStorage.setItem(sidebarScrollKey, sidebar.scrollTop);
        }, { passive: true });
    }
});

