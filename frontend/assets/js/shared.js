// /frontend/assets/js/shared.js
/**
 * Shared Frontend JavaScript Library
 * Features client-side navbar link auto-highlighting, dynamic validations, and visual enhancements.
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Dynamic Active Navbar Item Highlighting
  const currentPath = window.location.pathname;
  const pathSegments = currentPath.split('/');
  let currentPage = pathSegments[pathSegments.length - 1];
  
  // Default to index.php if path ends with trailing slash
  if (!currentPage || currentPage === '') {
    currentPage = 'index.php';
  }

  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
  let matched = false;

  navLinks.forEach(function (link) {
    const href = link.getAttribute('href');
    if (href) {
      const hrefSegments = href.split('/');
      const targetPage = hrefSegments[hrefSegments.length - 1];

      if (currentPage === targetPage) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
        matched = true;
      } else {
        link.classList.remove('active');
      }
    }
  });

  // Fallback: If no exact page matched, check if it starts with home link
  if (!matched && currentPage === 'index.php') {
    const homeLink = document.querySelector('.navbar-nav .nav-link[href*="index.php"]');
    if (homeLink) {
      homeLink.classList.add('active');
      homeLink.setAttribute('aria-current', 'page');
    }
  }

  // 2. Alert dismissal micro-animations
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      if (typeof bootstrap !== 'undefined') {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        if (bsAlert) {
          // Soft dismiss transition
          alert.classList.add('fade');
          setTimeout(() => bsAlert.close(), 150);
        }
      }
    }, 4000); // Auto-dismiss after 4 seconds
  });
});
