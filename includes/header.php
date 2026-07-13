<?php
/**
 * BizPulse — Shared Header (includes/header.php)
 *
 * Outputs the full <html>, <head>, and sticky navbar.
 * Supports:
 *  - Dark mode (Tailwind `class` strategy, anti-flash script)
 *  - Auth-aware navigation (shows Logout if logged in)
 *  - Responsive mobile menu
 *  - SVG Favicon
 *
 * Expected variables (set by the including page before this include):
 *  $pageTitle       string  — <title> tag content (optional)
 *  $metaDescription string  — <meta description> content (optional)
 *
 * Usage on protected pages:
 *   requireAuth() must be called BEFORE including this file.
 */

// ── Ensure auth helpers are available ────────────────────────────────────────
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}

$_navLoggedIn = isLoggedIn();
$_navUser     = $_navLoggedIn ? currentUser() : [];
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= isset($metaDescription) ? htmlspecialchars($metaDescription) : 'BizPulse – Helping Businesses Grow Digitally. Web Design, SEO & Content Management.' ?>" />
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | BizPulse' : 'BizPulse – Helping Businesses Grow Digitally' ?></title>

    <!-- ── SVG Favicon ──────────────────────────────────────────────────── -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='24' height='24' rx='6' fill='%234f46e5'/><path d='M13 3L4 14h7v7l9-11h-7z' fill='white'/></svg>" />

    <!-- ── DARK MODE: apply BEFORE CSS renders to prevent flash ────────── -->
    <script>
        (function () {
            var saved  = localStorage.getItem('bpTheme');
            var prefer = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefer)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- ── Tailwind CSS CDN ─────────────────────────────────────────────── -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ── Google Fonts — Inter ─────────────────────────────────────────── -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <!-- ── Tailwind Config ──────────────────────────────────────────────── -->
    <script>
        tailwind.config = {
            darkMode: 'class',   // Toggle dark mode via the `dark` class on <html>
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                    },
                    animation: {
                        'fade-in':    'fadeIn 0.5s ease-out',
                        'slide-up':   'slideUp 0.4s ease-out',
                        'scale-in':   'scaleIn 0.3s ease-out',
                        'bounce-in':  'bounceIn 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%':   { opacity: '0', transform: 'translateY(12px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideUp: {
                            '0%':   { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%':   { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        bounceIn: {
                            '0%':   { opacity: '0', transform: 'scale(0.3)' },
                            '50%':  { opacity: '1', transform: 'scale(1.05)' },
                            '70%':  { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)' },
                        },
                    },
                },
            },
        };
    </script>

    <!-- ── Custom Styles ────────────────────────────────────────────────── -->
    <link rel="stylesheet" href="/assets/css/style.css" />
</head>

<body class="font-sans bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 antialiased transition-colors duration-300">

<!-- ═══════════════════════════════════════════════════════════════════════════
     NAVIGATION
     ═══════════════════════════════════════════════════════════════════════════ -->
<nav class="bg-white/80 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50 shadow-sm transition-colors duration-300"
     role="navigation" aria-label="Main navigation">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- ── Logo ─────────────────────────────────────────────────── -->
            <a href="/index.php" class="flex items-center gap-2.5 group" aria-label="BizPulse Home">
                <!--
                    LOGO PLACEHOLDER:
                    To use your own logo image, replace this block with:
                    <img src="/assets/images/logo.png" alt="BizPulse" class="h-8 w-auto" />
                -->
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">
                    BizPulse
                </span>
            </a>

            <!-- ── Desktop Navigation ───────────────────────────────────── -->
            <div class="hidden md:flex items-center gap-6">

                <?php if ($_navLoggedIn): ?>
                    <!-- Logged-in nav: show dashboard + user info + logout -->
                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        👋 <?= htmlspecialchars($_navUser['name'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a href="/index.php"
                       class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        View Website
                    </a>
                    <a href="/logout.php"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                       aria-label="Sign out">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>

                <?php else: ?>
                    <!-- Public nav: services, contact, admin -->
                    <a href="/index.php#services"
                       class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                       Services
                    </a>
                    <a href="/index.php#contact"
                       class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                       Contact
                    </a>
                    <a href="/admin.php"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg shadow hover:shadow-indigo-300/50 hover:scale-105 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Admin
                    </a>
                <?php endif; ?>

                <!-- ── Dark Mode Toggle ──────────────────────────────────── -->
                <button id="theme-toggle"
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
                        aria-label="Toggle dark mode"
                        title="Toggle dark / light mode">
                    <!-- Moon — visible in light mode -->
                    <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <!-- Sun — visible in dark mode -->
                    <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

            </div><!-- /Desktop nav -->

            <!-- ── Mobile: dark toggle + hamburger ──────────────────────── -->
            <div class="md:hidden flex items-center gap-2">
                <button id="theme-toggle-mobile"
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
                        aria-label="Toggle dark mode">
                    <svg id="theme-icon-moon-m" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg id="theme-icon-sun-m" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <button id="mobile-menu-btn"
                        class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                        aria-label="Toggle navigation menu"
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div><!-- /flex row -->

        <!-- ── Mobile Menu ─────────────────────────────────────────────── -->
        <div id="mobile-menu" class="md:hidden hidden border-t border-slate-100 dark:border-slate-700 py-3 space-y-1">
            <?php if ($_navLoggedIn): ?>
                <div class="px-3 py-2 text-xs text-slate-500 dark:text-slate-400">
                    Signed in as <?= htmlspecialchars($_navUser['name'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <a href="/index.php" class="block px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">View Website</a>
                <a href="/logout.php" class="block px-3 py-2 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">Logout</a>
            <?php else: ?>
                <a href="/index.php#services" class="block px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">Services</a>
                <a href="/index.php#contact"  class="block px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors">Contact</a>
                <a href="/admin.php"          class="block px-3 py-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">Admin Dashboard</a>
            <?php endif; ?>
        </div>

    </div><!-- /container -->
</nav>
<!-- ═══════════════════════════════════════════════════════════════════════════
     /NAVIGATION
     ═══════════════════════════════════════════════════════════════════════════ -->

<!-- Dark mode script + mobile menu (shared across all pages using this header) -->
<script src="/assets/js/darkmode.js"></script>
<script>
(function () {
    // ── Mobile dark toggle (mirrors desktop) ─────────────────────────────
    var mobileBtn = document.getElementById('theme-toggle-mobile');
    if (mobileBtn) {
        mobileBtn.addEventListener('click', function () {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('bpTheme', isDark ? 'dark' : 'light');
            // Sync all four icons
            ['theme-icon-moon','theme-icon-sun','theme-icon-moon-m','theme-icon-sun-m'].forEach(function(id) {
                var el = document.getElementById(id);
                if (!el) return;
                var isSun = id.includes('sun');
                el.classList.toggle('hidden', isSun !== isDark);
            });
        });
    }

    // ── Mobile hamburger menu ─────────────────────────────────────────────
    var btn  = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.addEventListener('click', function () {
            var isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    }
})();
</script>
