<?php
/**
 * BizPulse — Admin Login Page (login.php)
 *
 * Standalone page (does not use includes/header.php).
 * Handles both GET (show form) and POST (process login).
 *
 * Security:
 *  - session_regenerate_id(true) after successful login (prevents session fixation)
 *  - password_verify() for bcrypt hash comparison (never plain-text comparison)
 *  - Generic error message for wrong credentials (never reveals which field is wrong)
 *  - HTTPOnly session cookie
 *  - POST-only for form submission
 *
 * Error handling (v2.1 fix):
 *  - getDB() now throws PDOException instead of calling die(json_encode(...))
 *  - PDOException is caught here and rendered as a clean HTML error message
 *  - Raw JSON is never exposed in the browser for UI pages
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// ── Start hardened session ────────────────────────────────────────────────
startSecureSession();

// ── Redirect authenticated admins to the dashboard ────────────────────────
if (isLoggedIn()) {
    header('Location: /admin.php');
    exit;
}

// ── Process login form (POST only) ───────────────────────────────────────
$error      = '';
$emailValue = ''; // Repopulate email field on error (never repopulate password)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawEmail    = $_POST['email']    ?? '';
    $rawPassword = $_POST['password'] ?? '';

    // Sanitise: strip illegal characters from email, trim whitespace from both
    $email    = trim(filter_var($rawEmail, FILTER_SANITIZE_EMAIL));
    $password = trim($rawPassword); // Do NOT strip characters from passwords

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } else {
        try {
            // getDB() now throws PDOException on failure — caught below
            $pdo = getDB();

            // Fetch the user record by email using a prepared statement
            $stmt = $pdo->prepare(
                'SELECT id, name, email, password, role
                 FROM   users
                 WHERE  email = :email
                 LIMIT  1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // Verify password against the stored bcrypt hash
            if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {

                // ── Successful login ──────────────────────────────────────
                // Regenerate session ID to prevent session fixation attacks.
                // The old session data is migrated to the new ID.
                session_regenerate_id(true);

                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role']       = $user['role'];

                // PRG pattern: redirect after POST to prevent form re-submission
                header('Location: /admin.php?logged_in=1');
                exit;

            } else {
                // Generic message — never reveal which field (email or password) is wrong
                $error      = 'Invalid email or password. Please try again.';
                $emailValue = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            }

        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'Something went wrong. Please try again shortly.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="BizPulse Admin Login — Secure access to the lead management dashboard." />
    <meta name="robots" content="noindex, nofollow" />
    <title>Admin Login | BizPulse</title>

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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                },
            },
        };
    </script>

    <link rel="stylesheet" href="/assets/css/style.css" />
</head>

<body class="font-sans antialiased min-h-screen login-bg flex items-center justify-center p-4">

<!-- ── Dark Mode Toggle (fixed position) ──────────────────────────────────── -->
<div class="fixed top-4 right-4 z-50">
    <button id="theme-toggle"
            class="p-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 backdrop-blur transition-all"
            aria-label="Toggle dark mode">
        <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>
</div>

<div class="w-full max-w-md animate-scale-in">

    <!-- ── Logo ─────────────────────────────────────────────────────────── -->
    <div class="text-center mb-8">
        <a href="/index.php" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white">BizPulse</span>
        </a>
        <p class="text-slate-400 text-sm mt-3">Lead Management System</p>
    </div>

    <!-- ── Login Card ────────────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-black/20 p-8 border border-white/10">

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Admin Sign In</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Sign in to access the dashboard</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl animate-slide-down" role="alert" aria-live="polite">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-700 dark:text-red-300"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="/login.php" novalidate id="login-form">

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Email Address
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= $emailValue ?>"
                       class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm placeholder-slate-400 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-600"
                       placeholder="admin@bizpulse.com"
                       required
                       autocomplete="email"
                       maxlength="150" />
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm placeholder-slate-400 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-600 pr-11"
                           placeholder="••••••••"
                           required
                           autocomplete="current-password"
                           maxlength="255" />
                    <!-- Show/hide password toggle -->
                    <button type="button"
                            id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                            aria-label="Toggle password visibility">
                        <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    id="login-btn"
                    class="btn-primary w-full py-3.5 px-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/30 text-sm">
                <span id="btn-text">Sign In to Dashboard</span>
                <span id="btn-loading" class="hidden">Signing in…</span>
            </button>
        </form>

        <!-- Default credentials hint (remove in production) -->
        <div class="mt-5 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
            <p class="text-xs text-amber-700 dark:text-amber-400 text-center">
                <strong>Demo credentials:</strong> admin@bizpulse.com &nbsp;/&nbsp; Admin@123
            </p>
        </div>
    </div>

    <!-- Back to site link -->
    <div class="text-center mt-6">
        <a href="/index.php" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Website
        </a>
    </div>
</div>

<!-- ── Scripts ─────────────────────────────────────────────────────────────── -->
<script src="/assets/js/darkmode.js"></script>
<script>
(function () {
    'use strict';

    // Password show/hide toggle
    var toggleBtn = document.getElementById('toggle-password');
    var pwdInput  = document.getElementById('password');
    var eyeOpen   = document.getElementById('eye-open');
    var eyeClosed = document.getElementById('eye-closed');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            var isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', isPassword);
            eyeClosed.classList.toggle('hidden', !isPassword);
        });
    }

    // Loading state on submit
    var form    = document.getElementById('login-form');
    var btnText = document.getElementById('btn-text');
    var btnLoad = document.getElementById('btn-loading');
    var btn     = document.getElementById('login-btn');

    if (form) {
        form.addEventListener('submit', function () {
            if (btn) btn.disabled = true;
            if (btnText) btnText.classList.add('hidden');
            if (btnLoad) btnLoad.classList.remove('hidden');
        });
    }
})();
</script>

</body>
</html>
