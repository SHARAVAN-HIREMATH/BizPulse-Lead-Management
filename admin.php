<?php
/**
 * BizPulse — Admin Dashboard (admin.php)
 *
 * Displays:
 *  - Statistics cards (Total / New / Contacted leads)
 *  - Responsive leads table (newest first)
 *  - Per-row AJAX status update button
 *
 * In a real application this page would be protected by authentication.
 * For this interview demo it is intentionally left open.
 */

// ── Page meta ─────────────────────────────────────────────────────────────
$pageTitle       = 'Lead Dashboard';
$metaDescription = 'BizPulse admin dashboard — manage and track all customer enquiries.';

// ── Database connection ───────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

// ── Fetch statistics ──────────────────────────────────────────────────────
try {
    $pdo = getDB();

    // Total leads
    $totalLeads = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();

    // New leads
    $newLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();

    // Contacted leads
    $contactedLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();

    // All leads — newest first
    $stmt  = $pdo->query('SELECT * FROM leads ORDER BY created_at DESC');
    $leads = $stmt->fetchAll(); // returns array of assoc arrays

} catch (PDOException $e) {
    error_log('Admin dashboard error: ' . $e->getMessage());
    $leads          = [];
    $totalLeads     = 0;
    $newLeads       = 0;
    $contactedLeads = 0;
}

// ── Include header ────────────────────────────────────────────────────────
require_once __DIR__ . '/includes/header.php';
?>

<!-- ══════════════════════════════════════════════════════════════════════
     ADMIN DASHBOARD HEADER
     ══════════════════════════════════════════════════════════════════════ -->
<div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-700 text-white" role="banner">
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
                    Manage and track all customer enquiries in real time
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

    <!-- ══════════════════════════════════════════════════════════════════
         STATISTICS CARDS
         ══════════════════════════════════════════════════════════════════ -->
    <section aria-label="Lead statistics" class="mb-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 stagger-children">

            <!-- Total Leads -->
            <div class="animate-fade-in-up bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Total Leads</p>
                    <p class="text-4xl font-extrabold text-slate-900 leading-none mt-1" id="stat-total">
                        <?= htmlspecialchars((string) $totalLeads) ?>
                    </p>
                </div>
            </div>

            <!-- New Leads -->
            <div class="animate-fade-in-up bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">New Leads</p>
                    <p class="text-4xl font-extrabold text-amber-600 leading-none mt-1" id="stat-new">
                        <?= htmlspecialchars((string) $newLeads) ?>
                    </p>
                </div>
            </div>

            <!-- Contacted Leads -->
            <div class="animate-fade-in-up bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Contacted</p>
                    <p class="text-4xl font-extrabold text-emerald-600 leading-none mt-1" id="stat-contacted">
                        <?= htmlspecialchars((string) $contactedLeads) ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════════
         LEADS TABLE
         ══════════════════════════════════════════════════════════════════ -->
    <section aria-labelledby="leads-table-heading">

        <!-- Table header bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h2 id="leads-table-heading" class="text-lg font-bold text-slate-900">All Enquiries</h2>
                <p class="text-sm text-slate-500">Sorted by most recent first</p>
            </div>

            <!-- Live search -->
            <div class="relative">
                <input type="search"
                       id="lead-search"
                       placeholder="Search leads…"
                       class="form-input pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white w-full sm:w-64"
                       aria-label="Search leads" />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Responsive card layout (mobile) / table (desktop) -->
        <?php if (empty($leads)): ?>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
            <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-slate-900 font-semibold text-lg mb-1">No Leads Yet</h3>
            <p class="text-slate-500 text-sm">
                When visitors submit the contact form, their enquiries will appear here.
            </p>
            <a href="/index.php#contact"
               class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                View Contact Form
            </a>
        </div>

        <?php else: ?>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="leads-table" aria-label="Customer leads table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
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
                    <tbody class="divide-y divide-slate-50" id="leads-tbody">

                        <?php foreach ($leads as $lead):
                            // All output is escaped via htmlspecialchars() — prevents XSS
                            $id          = (int) $lead['id'];
                            $safeName    = htmlspecialchars($lead['name'],    ENT_QUOTES, 'UTF-8');
                            $safeEmail   = htmlspecialchars($lead['email'],   ENT_QUOTES, 'UTF-8');
                            $safeService = htmlspecialchars($lead['service'], ENT_QUOTES, 'UTF-8');
                            $safeMessage = htmlspecialchars($lead['message'], ENT_QUOTES, 'UTF-8');
                            $safeDate    = htmlspecialchars(date('d M Y, H:i', strtotime($lead['created_at'])), ENT_QUOTES, 'UTF-8');
                            $status      = $lead['status'];
                            $isNew       = ($status === 'New');

                            // Badge styling
                            $badgeClass = $isNew
                                ? 'bg-amber-100 text-amber-700'
                                : 'bg-emerald-100 text-emerald-700';

                            // Button styling
                            $btnClass = $isNew
                                ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 cursor-pointer'
                                : 'bg-emerald-100 text-emerald-700 border-emerald-200 cursor-default';
                            $btnText     = $isNew ? 'Mark as Contacted' : '✓ Contacted';
                            $btnDisabled = $isNew ? '' : 'disabled';
                        ?>
                        <tr class="lead-row" data-name="<?= strtolower($safeName) ?>" data-email="<?= strtolower($safeEmail) ?>" data-service="<?= strtolower($safeService) ?>">
                            <!-- ID -->
                            <td class="px-5 py-4 font-mono text-xs text-slate-400">#<?= $id ?></td>

                            <!-- Name -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar initial -->
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" aria-hidden="true">
                                        <?= htmlspecialchars(strtoupper(substr($lead['name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <span class="font-medium text-slate-800"><?= $safeName ?></span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-5 py-4">
                                <a href="mailto:<?= $safeEmail ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                                    <?= $safeEmail ?>
                                </a>
                            </td>

                            <!-- Service -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                    <?= $safeService ?>
                                </span>
                            </td>

                            <!-- Message (truncated) -->
                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-slate-600 line-clamp-2 text-xs leading-relaxed" title="<?= $safeMessage ?>">
                                    <?= $safeMessage ?>
                                </p>
                            </td>

                            <!-- Date -->
                            <td class="px-5 py-4 text-slate-500 text-xs whitespace-nowrap">
                                <?= $safeDate ?>
                            </td>

                            <!-- Status badge -->
                            <td class="px-5 py-4 text-center" id="status-cell-<?= $id ?>">
                                <span id="status-badge-<?= $id ?>"
                                      class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>

                            <!-- Action button -->
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
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                Showing <span id="visible-count"><?= count($leads) ?></span> of <?= count($leads) ?> lead(s)
            </div>
        </div>

        <!-- ─────────────────────────────────────────────────────────────────
             MOBILE CARDS (visible only on small screens)
             ───────────────────────────────────────────────────────────────── -->
        <div class="md:hidden space-y-4 animate-fade-in-up" id="mobile-leads">
            <?php foreach ($leads as $lead):
                $id          = (int) $lead['id'];
                $safeName    = htmlspecialchars($lead['name'],    ENT_QUOTES, 'UTF-8');
                $safeEmail   = htmlspecialchars($lead['email'],   ENT_QUOTES, 'UTF-8');
                $safeService = htmlspecialchars($lead['service'], ENT_QUOTES, 'UTF-8');
                $safeMessage = htmlspecialchars($lead['message'], ENT_QUOTES, 'UTF-8');
                $safeDate    = htmlspecialchars(date('d M Y, H:i', strtotime($lead['created_at'])), ENT_QUOTES, 'UTF-8');
                $status      = $lead['status'];
                $isNew       = ($status === 'New');

                $badgeClass = $isNew ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700';
                $btnClass   = $isNew
                    ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                    : 'bg-emerald-100 text-emerald-700 border-emerald-200 cursor-default';
                $btnText    = $isNew ? 'Mark as Contacted' : '✓ Contacted';
                $btnDisabled = $isNew ? '' : 'disabled';
            ?>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            <?= htmlspecialchars(strtoupper(substr($lead['name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm"><?= $safeName ?></p>
                            <a href="mailto:<?= $safeEmail ?>" class="text-xs text-indigo-600"><?= $safeEmail ?></a>
                        </div>
                    </div>
                    <span id="status-badge-mobile-<?= $id ?>"
                          class="status-badge inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600 mb-3">
                    <p><span class="font-medium text-slate-700">Service:</span> <?= $safeService ?></p>
                    <p><span class="font-medium text-slate-700">Date:</span> <?= $safeDate ?></p>
                    <p class="text-slate-500 line-clamp-2"><?= $safeMessage ?></p>
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

<!-- ── Admin JS + live search ────────────────────────────────────────────── -->
<script src="/assets/js/admin.js"></script>
<script>
/**
 * Live search — filters table rows as the user types.
 * Searches across name, email, and service columns.
 */
(function () {
    'use strict';

    const searchInput  = document.getElementById('lead-search');
    const rows         = document.querySelectorAll('#leads-tbody .lead-row');
    const visibleCount = document.getElementById('visible-count');

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let count   = 0;

        rows.forEach(function (row) {
            const name    = row.dataset.name    || '';
            const email   = row.dataset.email   || '';
            const service = row.dataset.service || '';
            const matches = name.includes(query) || email.includes(query) || service.includes(query);

            row.style.display = matches ? '' : 'none';
            if (matches) count++;
        });

        if (visibleCount) visibleCount.textContent = String(count);
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
