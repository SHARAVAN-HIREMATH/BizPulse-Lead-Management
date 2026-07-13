<?php
/**
 * BizPulse — Custom 404 Error Page (404.php)
 *
 * Standalone page — does not use includes/header.php.
 * Rendered whenever a missing page is accessed (configure in .htaccess).
 */
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>404 — Page Not Found | BizPulse</title>

    <!-- SVG Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect width='24' height='24' rx='6' fill='%234f46e5'/><path d='M13 3L4 14h7v7l9-11h-7z' fill='white'/></svg>" />

    <!-- Dark mode anti-flash -->
    <script>
        (function () {
            var s = localStorage.getItem('bpTheme');
            var p = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (s === 'dark' || (!s && p)) document.documentElement.classList.add('dark');
        })();
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif'] } } }
        };
    </script>

    <link rel="stylesheet" href="/assets/css/style.css" />
</head>

<body class="font-sans antialiased min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 flex flex-col transition-colors duration-300">

<!-- Dark Mode Toggle -->
<div class="fixed top-4 right-4 z-50">
    <button id="theme-toggle"
            class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm transition-all"
            aria-label="Toggle dark mode">
        <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>
</div>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-lg animate-scale-in">

        <!-- Logo -->
        <a href="/index.php" class="inline-flex items-center gap-2 justify-center mb-10 group">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">BizPulse</span>
        </a>

        <!-- 404 Illustration -->
        <div class="relative mb-8">
            <!-- SVG Illustration -->
            <svg class="w-56 h-56 mx-auto" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- Background circle -->
                <circle cx="110" cy="110" r="100" fill="currentColor" class="text-indigo-50 dark:text-indigo-900/30"/>
                <!-- Document -->
                <rect x="65" y="55" width="90" height="110" rx="8" fill="currentColor" class="text-white dark:text-slate-700" stroke="currentColor" stroke-class="text-indigo-200"/>
                <rect x="65" y="55" width="90" height="110" rx="8" fill="none" stroke="#c7d2fe" stroke-width="2"/>
                <!-- Lines on document -->
                <rect x="80" y="80" width="60" height="6" rx="3" fill="#e0e7ff"/>
                <rect x="80" y="95" width="45" height="6" rx="3" fill="#e0e7ff"/>
                <rect x="80" y="110" width="52" height="6" rx="3" fill="#e0e7ff"/>
                <!-- Question mark -->
                <circle cx="110" cy="145" r="22" fill="#4f46e5"/>
                <text x="110" y="152" text-anchor="middle" fill="white" font-size="20" font-weight="700" font-family="Inter, sans-serif">?</text>
                <!-- Decorative dots -->
                <circle cx="55" cy="75" r="6" fill="#c7d2fe"/>
                <circle cx="165" cy="150" r="8" fill="#818cf8"/>
                <circle cx="50" cy="145" r="4" fill="#e0e7ff"/>
                <circle cx="175" cy="80" r="5" fill="#c7d2fe"/>
            </svg>
        </div>

        <!-- 404 Text -->
        <div class="mb-4">
            <span class="text-8xl font-black bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent leading-none">
                404
            </span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
            Oops! Page Not Found
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8 max-w-sm mx-auto">
            The page you're looking for doesn't exist or may have been moved.
            Let's get you back on track.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/index.php"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-300/50 hover:scale-105 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Return Home
            </a>
            <button onclick="history.back()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </button>
        </div>

    </div>
</main>

<!-- Footer -->
<footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-600 border-t border-slate-100 dark:border-slate-800">
    &copy; <?= date('Y') ?> BizPulse. All rights reserved.
</footer>

<script src="/assets/js/darkmode.js"></script>
</body>
</html>
