/**
 * Header behavior: add sticky/scroll classes and wire wishlist drawer.
 */
(function () {
    const STICKY_AT = 120; // px from top before switching to dark pink

    function toggleScrollState() {
        const y = window.scrollY || window.pageYOffset || 0;
        const headerPart = document.querySelector('header.wp-block-template-part');

        if (y > STICKY_AT) {
            document.body.classList.add('has-scrolled-header');
            if (headerPart) headerPart.classList.add('is-sticky');
        } else {
            document.body.classList.remove('has-scrolled-header');
            if (headerPart) headerPart.classList.remove('is-sticky');
        }
    }

    function initWishlist() {
        const toggleBtn = document.querySelector('.header-wishlist-toggle');
        const panel = document.getElementById('wishlist-panel');
        if (!toggleBtn || !panel) return;

        const overlay = panel.querySelector('.wishlist-panel__overlay');
        const closeBtn = panel.querySelector('.wishlist-panel__close');

        function closePanel() {
            panel.classList.remove('is-open');
        }

        toggleBtn.addEventListener('click', function () {
            panel.classList.add('is-open');
        });

        if (overlay) overlay.addEventListener('click', closePanel);
        if (closeBtn) closeBtn.addEventListener('click', closePanel);
        document.addEventListener('keydown', function (evt) {
            if (evt.key === 'Escape' && panel.classList.contains('is-open')) {
                closePanel();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleScrollState();
        window.addEventListener('scroll', toggleScrollState, { passive: true });
        initWishlist();
    });
})();
