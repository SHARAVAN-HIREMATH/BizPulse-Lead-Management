/**
 * BizPulse — admin.js
 *
 * Handles AJAX status updates for the admin dashboard.
 *
 * Flow:
 *  1. User clicks "Mark as Contacted" button on a lead row.
 *  2. We POST the lead ID to update_status.php via the Fetch API.
 *  3. On success we update the badge colour and button state inline
 *     — no full page refresh required.
 *
 * Why Fetch API over XMLHttpRequest?
 *  - Promise-based → cleaner async/await syntax
 *  - Built into modern browsers (no library needed)
 *  - Easier to handle JSON responses
 */

'use strict';

/**
 * updateLeadStatus
 *
 * @param {number} leadId       - The database ID of the lead to update
 * @param {HTMLButtonElement} btn - The button that was clicked
 */
async function updateLeadStatus(leadId, btn) {

    // ── 1. Optimistic UI: disable button and show loading state ──────────
    btn.disabled    = true;
    btn.textContent = 'Updating…';
    btn.classList.add('opacity-75', 'cursor-not-allowed');

    try {
        // ── 2. POST to update_status.php ──────────────────────────────────
        const response = await fetch('/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',   // marks request as AJAX
            },
            // Send lead ID as URL-encoded body (safe — no string concatenation)
            body: new URLSearchParams({ id: leadId }),
        });

        // ── 3. Parse JSON response ────────────────────────────────────────
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            // ── 4a. Success: update badge and button ──────────────────────
            const badge = document.getElementById(`status-badge-${leadId}`);
            const cell  = document.getElementById(`status-cell-${leadId}`);

            if (badge) {
                badge.textContent = 'Contacted';
                badge.className   =
                    'status-badge inline-flex items-center px-2.5 py-0.5 rounded-full ' +
                    'text-xs font-semibold bg-emerald-100 text-emerald-700';
            }

            btn.textContent = '✓ Contacted';
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btn.classList.add(
                'bg-emerald-100', 'text-emerald-700',
                'border-emerald-200', 'cursor-default'
            );
            btn.classList.remove(
                'bg-amber-50', 'text-amber-700',
                'border-amber-200', 'hover:bg-amber-100'
            );

            // Keep button disabled (action already done)
            btn.disabled = true;

            showToast('Lead status updated to Contacted!', 'success');
        } else {
            // ── 4b. Server returned success:false ─────────────────────────
            throw new Error(data.message || 'Update failed.');
        }

    } catch (error) {
        // ── 5. Error: restore button and show error toast ─────────────────
        console.error('Status update error:', error);

        btn.disabled    = false;
        btn.textContent = 'Mark as Contacted';
        btn.classList.remove('opacity-75', 'cursor-not-allowed');

        showToast('Failed to update status. Please try again.', 'error');
    }
}


/**
 * showToast
 *
 * Displays a temporary toast notification at the bottom of the screen.
 *
 * @param {string} message
 * @param {'success'|'error'} type
 */
function showToast(message, type = 'success') {
    // Remove existing toast if any
    const existing = document.getElementById('bp-toast');
    if (existing) existing.remove();

    const colours = type === 'success'
        ? 'bg-emerald-600 text-white'
        : 'bg-red-600 text-white';

    const toast = document.createElement('div');
    toast.id        = 'bp-toast';
    toast.className =
        `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl
         text-sm font-medium ${colours}
         animate-fade-in-up flex items-center gap-2`;

    const icon = type === 'success' ? '✓' : '✕';
    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;

    document.body.appendChild(toast);

    // Auto-dismiss after 3.5 seconds
    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease';
        toast.style.opacity    = '0';
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}
