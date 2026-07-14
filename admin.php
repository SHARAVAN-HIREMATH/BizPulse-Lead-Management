<?php
/**
 * BizPulse — Admin Dashboard (admin.php) v2
 *
 * Protected page: requires admin authentication.
 *
 * Features added in v2:
 *  - requireAuth() guard — redirects to login.php if unauthenticated
 *  - Logout button in header (via auth-aware navigation in header.php)
 *  - Welcome toast after login
 *  - Dynamic stat counters (updated via AJAX without reload)
 *  - Full dark mode support
 *  - Improved responsive table + mobile card layout
 *  - Live search
 *
 * Error handling (v2.1 fix):
 *  - getDB() now throws PDOException instead of die(json_encode(...))
 *  - PDOException is caught here; a clean HTML error banner is shown
 *  - Raw JSON is never exposed in the browser
 */

// ── MUST be first — before any HTML output ────────────────────────────────
require_once __DIR__ . '/includes/auth.php';
requireAuth(); // Redirect to login.php if not authenticated

// ── Load dependencies ─────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

// ── Get current user for the dashboard ───────────────────────────────────
$user = currentUser();

// ── Detect post-login redirect ────────────────────────────────────────────
$justLoggedIn = isset($_GET['logged_in']) && $_GET['logged_in'] === '1';

// ── Fetch statistics ──────────────────────────────────────────────────────
// getDB() now throws PDOException on failure (v2.1 fix).
// If the DB is unreachable, show zeroed stats and a visible error banner.
$dbError = false;
try {
    $pdo = getDB();

    $totalLeads     = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    $newLeads       = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
    $contactedLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();

    // All leads — newest first
    $leads = $pdo->query('SELECT * FROM leads ORDER BY created_at DESC')->fetchAll();

} catch (PDOException $e) {
    // Log the technical details; never expose to the user
    error_log('[BizPulse] Admin dashboard DB error: ' . $e->getMessage());
    $leads          = [];
    $totalLeads     = 0;
    $newLeads       = 0;
    $contactedLeads = 0;
    $dbError        = true;
}

// ── Page meta ─────────────────────────────────────────────────────────────
$pageTitle       = 'Lead Dashboard';
$metaDescription = 'BizPulse admin dashboard — manage and track all customer enquiries.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     DASHBOARD HEADER BANNER
     ═══════════════════════════════════════════════════════════════════════════ -->
<div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-700 dark:from-indigo-900 dark:via-indigo-800 dark:to-blue-900 text-white transition-colors duration-300" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">BizPulse Lead Dashboard</h1>
                </div>
                <p class="text-indigo-200 text-sm">
                    Welcome back, <strong class="text-white"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    &nbsp;·&nbsp; Last refreshed: <?= date('d M Y, H:i') ?>
                </p>
            </div>
            <a href="/index.php"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/15 hover:bg-white/25 backdrop-blur rounded-xl text-sm font-semibold transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                View Website
            </a>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <?php if ($dbError): ?>
    <!-- ── Database error banner ────────────────────────────────────────── -->
    <!-- Shown only when the DB is unreachable. Raw errors are never exposed. -->
    <div class="mb-8 flex items-start gap-4 p-5 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-2xl" role="alert">
        <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p class="font-semibold text-red-800 dark:text-red-200">Unable to load dashboard data</p>
            <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                The database is temporarily unreachable. Please try refreshing the page.
                If the problem persists, check your database configuration.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════════════════
         STATISTICS CARDS
         ═══════════════════════════════════════════════════════════════════════ -->
    <section aria-label="Lead statistics" class="mb-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 stagger-children">

            <!-- Total Leads -->
            <div class="stat-card animate-fade-in-up bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Leads</p>
                    <p class="text-4xl font-extrabold text-slate-900 dark:text-white leading-none mt-1 tabular-nums" id="stat-total">
                        <?= $totalLeads ?>
                    </p>
                </div>
            </div>

            <!-- New Leads -->
            <div class="stat-card animate-fade-in-up bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">New Leads</p>
                    <p class="text-4xl font-extrabold text-amber-600 dark:text-amber-400 leading-none mt-1 tabular-nums" id="stat-new">
                        <?= $newLeads ?>
                    </p>
                </div>
            </div>

            <!-- Contacted Leads -->
            <div class="stat-card animate-fade-in-up bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Contacted</p>
                    <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 leading-none mt-1 tabular-nums" id="stat-contacted">
                        <?= $contactedLeads ?>
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════════
         LEADS TABLE
         ═══════════════════════════════════════════════════════════════════════ -->
    <section aria-labelledby="leads-table-heading">

        <!-- Header bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h2 id="leads-table-heading" class="text-lg font-bold text-slate-900 dark:text-white">All Enquiries</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Sorted by most recent first</p>
            </div>
            <!-- Live search -->
            <div class="relative">
                <input type="search"
                       id="lead-search"
                       placeholder="Search leads…"
                       class="form-input pl-9 pr-4 py-2.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-xl text-sm w-full sm:w-64"
                       aria-label="Search leads" />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <?php if (empty($leads)): ?>
        <!-- Empty state -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-16 text-center">
            <svg class="w-16 h-16 text-slate-200 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-slate-900 dark:text-white font-semibold text-lg mb-1">No Leads Yet</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto">
                Enquiries submitted through the contact form will appear here.
            </p>
            <a href="/index.php#contact"
               class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                View Contact Form
            </a>
        </div>

        <?php else: ?>

        <!-- ── Desktop Table ─────────────────────────────────────────────── -->
        <div class="hidden md:block bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="leads-table" aria-label="Customer leads table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/60 border-b border-slate-100 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="text-left px-5 py-3.5" scope="col">#</th>
                            <th class="text-left px-5 py-3.5" scope="col">Name</th>
                            <th class="text-left px-5 py-3.5" scope="col">Email</th>
                            <th class="text-left px-5 py-3.5" scope="col">Service</th>
                            <th class="text-left px-5 py-3.5 max-w-xs" scope="col">Message</th>
                            <th class="text-left px-5 py-3.5" scope="col">Date</th>
                            <th class="text-center px-5 py-3.5" scope="col">Status</th>
                            <th class="text-center px-5 py-3.5" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700" id="leads-tbody">

                        <?php foreach ($leads as $lead):
                            $id          = (int) $lead['id'];
                            $safeName    = htmlspecialchars($lead['name'],    ENT_QUOTES, 'UTF-8');
                            $safeEmail   = htmlspecialchars($lead['email'],   ENT_QUOTES, 'UTF-8');
                            $safeService = htmlspecialchars($lead['service'], ENT_QUOTES, 'UTF-8');
                            $safeMessage = htmlspecialchars($lead['message'], ENT_QUOTES, 'UTF-8');
                            $safeDate    = date('d M Y, H:i', strtotime($lead['created_at']));
                            $status      = $lead['status'];
                            $isNew       = ($status === 'New');

                            $badgeClass = $isNew
                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                                : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400';

                            $btnClass = $isNew
                                ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/40 cursor-pointer'
                                : 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 cursor-default opacity-75';

                            $btnText     = $isNew ? 'Mark as Contacted' : '✓ Contacted';
                            $btnDisabled = $isNew ? '' : 'disabled';

                            $avatarLetter = htmlspecialchars(strtoupper(substr($lead['name'], 0, 1)), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="lead-row"
                            data-name="<?= strtolower($safeName) ?>"
                            data-email="<?= strtolower($safeEmail) ?>"
                            data-service="<?= strtolower($safeService) ?>">

                            <td class="px-5 py-4 font-mono text-xs text-slate-400 dark:text-slate-500">#<?= $id ?></td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" aria-hidden="true">
                                        <?= $avatarLetter ?>
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-slate-200"><?= $safeName ?></span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <a href="mailto:<?= $safeEmail ?>"
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline transition-colors text-xs">
                                    <?= $safeEmail ?>
                                </a>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                                    <?= $safeService ?>
                                </span>
                            </td>

                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-slate-600 dark:text-slate-400 line-clamp-2 text-xs leading-relaxed" title="<?= $safeMessage ?>">
                                    <?= $safeMessage ?>
                                </p>
                            </td>

                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                <?= htmlspecialchars($safeDate, ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td class="px-5 py-4 text-center" id="status-cell-<?= $id ?>">
                                <span id="status-badge-<?= $id ?>"
                                      class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <button id="btn-<?= $id ?>"
                                        onclick="updateLeadStatus(<?= $id ?>, this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-lg transition-all <?= $btnClass ?>"
                                        <?= $btnDisabled ?>
                                        aria-label="<?= $isNew ? "Mark lead #{$id} as contacted" : "Lead #{$id} already contacted" ?>">
                                    <?= $btnText ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- Table footer -->
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-700/40 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>Showing <span id="visible-count"><?= count($leads) ?></span> of <?= count($leads) ?> enquiry(s)</span>
                <span>Newest first</span>
            </div>
        </div>

        <!-- ── Mobile Cards ──────────────────────────────────────────────── -->
        <div class="md:hidden space-y-4 animate-fade-in-up" id="mobile-leads">
            <?php foreach ($leads as $lead):
                $id          = (int) $lead['id'];
                $safeName    = htmlspecialchars($lead['name'],    ENT_QUOTES, 'UTF-8');
                $safeEmail   = htmlspecialchars($lead['email'],   ENT_QUOTES, 'UTF-8');
                $safeService = htmlspecialchars($lead['service'], ENT_QUOTES, 'UTF-8');
                $safeMessage = htmlspecialchars($lead['message'], ENT_QUOTES, 'UTF-8');
                $safeDate    = date('d M Y, H:i', strtotime($lead['created_at']));
                $status      = $lead['status'];
                $isNew       = ($status === 'New');

                $badgeClass = $isNew
                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                    : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400';
                $btnClass   = $isNew
                    ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/40'
                    : 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 opacity-75';
                $btnText    = $isNew ? 'Mark as Contacted' : '✓ Contacted';
                $btnDisabled = $isNew ? '' : 'disabled';
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            <?= htmlspecialchars(strtoupper(substr($lead['name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white text-sm"><?= $safeName ?></p>
                            <a href="mailto:<?= $safeEmail ?>" class="text-xs text-indigo-600 dark:text-indigo-400"><?= $safeEmail ?></a>
                        </div>
                    </div>
                    <span class="status-badge inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 mb-3">
                    <p><span class="font-medium text-slate-700 dark:text-slate-300">Service:</span> <?= $safeService ?></p>
                    <p><span class="font-medium text-slate-700 dark:text-slate-300">Date:</span> <?= htmlspecialchars($safeDate, ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="line-clamp-2"><?= $safeMessage ?></p>
                </div>
                <button onclick="updateLeadStatus(<?= $id ?>, this)"
                        class="w-full py-2 text-xs font-semibold border rounded-lg transition-all <?= $btnClass ?>"
                        <?= $btnDisabled ?>>
                    <?= $btnText ?>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </section>

</main>

<!-- ── Scripts ───────────────────────────────────────────────────────────────── -->
<script src="/assets/js/admin.js"></script>
<script>
'use strict';

// ── Live Search ────────────────────────────────────────────────────────────
(function () {
    var searchInput  = document.getElementById('lead-search');
    var rows         = document.querySelectorAll('#leads-tbody .lead-row');
    var visibleCount = document.getElementById('visible-count');

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        var query = this.value.toLowerCase().trim();
        var count = 0;

        rows.forEach(function (row) {
            var matches =
                (row.dataset.name    || '').includes(query) ||
                (row.dataset.email   || '').includes(query) ||
                (row.dataset.service || '').includes(query);

            row.style.display = matches ? '' : 'none';
            if (matches) count++;
        });

        if (visibleCount) visibleCount.textContent = String(count);
    });
})();

// ── Welcome toast after login ──────────────────────────────────────────────
<?php if ($justLoggedIn): ?>
document.addEventListener('DOMContentLoaded', function () {
    showToast('Welcome back, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>! You are signed in.', 'success');
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
