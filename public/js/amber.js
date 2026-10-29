/*
 * Amber demo layout
 *
 * Data attributes:
 * - data-amber-menu-toggle - opens the sidebar on small screens
 * - data-amber-menu-close - closes the sidebar on small screens
 * - data-amber-nav-group-toggle - expands or collapses a sidebar group
 * - data-amber-theme-toggle - switches between the light and dark theme
 */
'use strict';

(function () {
    var THEME_KEY = 'amber-theme';

    function openMenu() {
        document.body.classList.add('sidebar-open');
        setExpanded('[data-amber-menu-toggle]', true);
    }

    function closeMenu() {
        document.body.classList.remove('sidebar-open');
        setExpanded('[data-amber-menu-toggle]', false);
    }

    function setExpanded(selector, isExpanded) {
        document.querySelectorAll(selector).forEach(function (el) {
            el.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        });
    }

    function toggleNavGroup(button) {
        var group = button.closest('[data-amber-nav-group]');
        if (!group) {
            return;
        }

        var isOpen = group.classList.toggle('show');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function toggleTheme() {
        var root = document.documentElement,
            theme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

        root.setAttribute('data-bs-theme', theme);

        try {
            localStorage.setItem(THEME_KEY, theme);
        }
        catch (e) {
            // Storage can be unavailable, the theme still applies to this page
        }
    }

    document.addEventListener('click', function (ev) {
        var target = ev.target;

        if (target.closest('[data-amber-menu-toggle]')) {
            openMenu();
        }
        else if (target.closest('[data-amber-menu-close]')) {
            closeMenu();
        }
        else if (target.closest('[data-amber-nav-group-toggle]')) {
            toggleNavGroup(target.closest('[data-amber-nav-group-toggle]'));
        }
        else if (target.closest('[data-amber-theme-toggle]')) {
            toggleTheme();
        }
    });

    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            closeMenu();
        }
    });
})();
