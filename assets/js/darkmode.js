/**
 * BizPulse — Dark Mode Manager (assets/js/darkmode.js)
 *
 * Handles dark/light theme toggling with localStorage persistence.
 * The CRITICAL anti-flash snippet is embedded inline in <head> of header.php.
 * This file manages the interactive toggle button behaviour.
 *
 * Flow:
 *  1. Page loads → header.php's <head> script reads localStorage and sets
 *     the `dark` class on <html> BEFORE any CSS renders (no flash).
 *  2. This file wires up the toggle button after the DOM is ready.
 *  3. Toggle click → flip the `dark` class on <html> + persist to localStorage.
 */

'use strict';

(function () {

    /**
     * Apply the saved or system-preferred theme.
     * Called once on DOMContentLoaded to sync the button icon state.
     */
    function syncToggleIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        updateIcon(isDark);
    }

    /**
     * Toggle between dark and light mode.
     */
    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('bpTheme', isDark ? 'dark' : 'light');
        updateIcon(isDark);
    }

    /**
     * Update the toggle button icon (sun ↔ moon).
     * @param {boolean} isDark
     */
    function updateIcon(isDark) {
        const sunIcon  = document.getElementById('theme-icon-sun');
        const moonIcon = document.getElementById('theme-icon-moon');

        if (!sunIcon || !moonIcon) return;

        // Dark mode active → show sun (click to go light)
        // Light mode active → show moon (click to go dark)
        sunIcon.classList.toggle('hidden', !isDark);
        moonIcon.classList.toggle('hidden', isDark);
    }

    // ── Wire up the toggle button ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        syncToggleIcon();

        const btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.addEventListener('click', toggleTheme);
        }
    });

})();
