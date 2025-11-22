// App custom scripts extracted from app.blade.php

document.addEventListener('DOMContentLoaded', function () {
  const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
  const Default = {
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'leave',
    scrollbarClickScroll: true,
  };

  // Initialize OverlayScrollbars for sidebar
  const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
  if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
    window.OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
      scrollbars: {
        theme: Default.scrollbarTheme,
        autoHide: Default.scrollbarAutoHide,
        clickScroll: Default.scrollbarClickScroll,
      },
    });
  }

  // Responsive sidebar behavior
  const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

  const syncSidebarState = () => {
    if (isMobile()) {
      document.body.classList.remove('sidebar-open');
      document.body.classList.add('sidebar-collapse');
    } else {
      document.body.classList.add('sidebar-open');
      document.body.classList.remove('sidebar-collapse');
    }
  };

  const ensureOverlay = () => {
    let ov = document.querySelector('.sidebar-overlay');
    if (!ov) {
      ov = document.createElement('div');
      ov.className = 'sidebar-overlay';
      ov.addEventListener('click', () => closeSidebar(), { passive: true });
      document.body.appendChild(ov);
    }
    return ov;
  };

  const removeOverlay = () => {
    const ov = document.querySelector('.sidebar-overlay');
    if (ov) ov.remove();
  };

  const openSidebar = () => {
    document.body.classList.add('sidebar-open');
    document.body.classList.remove('sidebar-collapse');
    if (isMobile()) ensureOverlay();
  };

  const closeSidebar = () => {
    document.body.classList.remove('sidebar-open');
    document.body.classList.add('sidebar-collapse');
    removeOverlay();
  };

  const closeSidebarOnMobile = () => { if (isMobile()) closeSidebar(); };

  // Initial state and resize handling
  syncSidebarState();
  let __rsz;
  window.addEventListener('resize', () => {
    clearTimeout(__rsz);
    __rsz = setTimeout(syncSidebarState, 150);
  });

  // Close sidebar after clicking any leaf link (including submenu items) on mobile
  document.querySelectorAll('.nav-treeview a.nav-link').forEach((link) => {
    link.addEventListener('click', closeSidebarOnMobile);
  });
  // Also handle top-level links (menu without submenu)
  document.querySelectorAll('.sidebar-menu a.nav-link').forEach((link) => {
    const href = link.getAttribute('href');
    if (href && href !== '#') {
      ['click', 'pointerdown', 'touchstart'].forEach((evt) => {
        link.addEventListener(evt, () => closeSidebarOnMobile(), { passive: true });
      });
    }
  });

  const bindToggle = (el) => {
    ['click', 'pointerdown', 'touchstart'].forEach((evt) => {
      el.addEventListener(evt, (e) => {
        e.preventDefault();
        e.stopPropagation();
        const open = document.body.classList.contains('sidebar-open');
        if (open) { closeSidebar(); } else { openSidebar(); }
      }, { passive: false });
    });
  };

  const headerHamburger = document.getElementById('headerHamburger');
  if (headerHamburger) bindToggle(headerHamburger);
  const sidebarHamburger = document.getElementById('sidebarHamburger');
  if (sidebarHamburger) bindToggle(sidebarHamburger);

  // Bind any element using data-lte-toggle="sidebar"
  document.querySelectorAll('[data-lte-toggle="sidebar"]').forEach((el) => bindToggle(el));

  // Prevent URL hash (#) when clicking anchors used as toggles
  document.addEventListener('click', function (e) {
    const a = e.target.closest('a[href="#"]');
    if (a) {
      e.preventDefault();
    }
  });
  // Failsafe: never leave overlay around
  window.addEventListener('pageshow', removeOverlay);
  window.addEventListener('beforeunload', removeOverlay);
});
