/**
 * BizPulse — Admin Dashboard JS (assets/js/admin.js) v2
 *
 * Handles:
 *  1. updateLeadStatus()  — AJAX lead status update via Fetch API
 *  2. Live dashboard stat counters (#stat-total, #stat-new, #stat-contacted)
 *  3. showToast()         — accessible toast notification (no browser alert)
 *
 * All DOM IDs are defined in admin.php.
 * All API responses are validated before acting on them.
 */

'use strict';

// ── Toast Container ────────────────────────────────────────────────────────
// Injected once on first call, used for all subsequent toasts.
let toastContainer = null;

function getToastContainer() {
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.setAttribute('role', 'region');
        toastContainer.setAttribute('aria-label', 'Notifications');
        toastContainer.setAttribute('aria-live', 'polite');
        toastContainer.style.cssText = [
            'position: fixed',
            'bottom: 1.5rem',
            'right: 1.5rem',
            'z-index: 9999',
            'display: flex',
            'flex-direction: column',
            'gap: 0.5rem',
            'pointer-events: none',
            'max-width: 22rem',
        ].join(';');
        document.body.appendChild(toastContainer);
    }
    return toastContainer;
}

/**
 * Show an accessible toast notification.
 *
 * @param {string} message   - The message to display
 * @param {'success'|'error'|'info'} type - Visual style
 * @param {number} duration  - Auto-dismiss delay in ms (default: 4000)
 */
function showToast(message, type = 'success', duration = 4000) {
    const container = getToastContainer();

    const colours = {
        success: {
            bg:     'linear-gradient(135deg, #10b981, #059669)',
            border: '#6ee7b7',
            icon:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />',
        },
        error: {
            bg:     'linear-gradient(135deg, #ef4444, #dc2626)',
            border: '#fca5a5',
            icon:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        },
        info: {
            bg:     'linear-gradient(135deg, #6366f1, #4f46e5)',
            border: '#a5b4fc',
            icon:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        },
    };

    const c     = colours[type] || colours.info;
    const toast = document.createElement('div');
    toast.setAttribute('role', 'alert');

    toast.style.cssText = [
        'display: flex',
        'align-items: center',
        'gap: 0.75rem',
        'padding: 0.875rem 1.125rem',
        'border-radius: 0.75rem',
        'color: white',
        'font-size: 0.875rem',
        'font-weight: 500',
        'font-family: Inter, ui-sans-serif, sans-serif',
        'pointer-events: auto',
        'cursor: pointer',
        'box-shadow: 0 10px 25px rgba(0,0,0,0.25)',
        `background: ${c.bg}`,
        `border: 1px solid ${c.border}40`,
    ].join(';');

    toast.className = 'toast-enter';
    toast.innerHTML =
        `<svg style="width:1.125rem;height:1.125rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">${c.icon}</svg>` +
        `<span>${message}</span>`;

    toast.addEventListener('click', () => dismiss(toast));
    container.appendChild(toast);

    function dismiss(el) {
        el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        el.style.opacity    = '0';
        el.style.transform  = 'translateX(100%)';
        setTimeout(() => { if (el.parentNode) el.parentNode.removeChild(el); }, 300);
    }

    setTimeout(() => dismiss(toast), duration);
}

// ── Update Live Stat Counters ──────────────────────────────────────────────
/**
 * Update the three dashboard stat counter elements.
 * Called after every successful AJAX status update.
 *
 * @param {{ total: number, newCount: number, contacted: number }} stats
 */
function updateStatCounters(stats) {
    if (!stats || typeof stats !== 'object') return;

    const elTotal     = document.getElementById('stat-total');
    const elNew       = document.getElementById('stat-new');
    const elContacted = document.getElementById('stat-contacted');

    function animateCounter(el, newValue) {
        if (!el) return;
        const old = parseInt(el.textContent, 10) || 0;
        if (old === newValue) return;

        // Brief scale-up animation to draw attention
        el.style.transition = 'transform 0.15s ease, opacity 0.15s ease';
        el.style.transform  = 'scale(1.25)';
        el.style.opacity    = '0.7';

        setTimeout(() => {
            el.textContent     = newValue;
            el.style.transform = 'scale(1)';
            el.style.opacity   = '1';
        }, 150);
    }

    animateCounter(elTotal,     stats.total     ?? 0);
    animateCounter(elNew,       stats.newCount  ?? 0);
    animateCounter(elContacted, stats.contacted ?? 0);
}

// ── Main: Update Lead Status ───────────────────────────────────────────────
/**
 * Called by the "Mark as Contacted" button in admin.php.
 *
 * @param {number}      id  - The lead ID (set by PHP in onclick attr)
 * @param {HTMLElement} btn - The button that was clicked
 */
async function updateLeadStatus(id, btn) {
    if (!id || !btn) return;

    // Disable button immediately to prevent double-clicks
    btn.disabled    = true;
    btn.textContent = 'Updating…';

    try {
        const response = await fetch('/update_status.php', {
            method:  'POST',
            headers: {
                'Content-Type':     'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: 'id=' + encodeURIComponent(id),
        });

        // ── Handle non-200 HTTP errors (auth, method, etc.) ───────────────
        if (!response.ok) {
            if (response.status === 401) {
                showToast('Session expired. Please log in again.', 'error');
                setTimeout(() => { window.location.href = '/login.php'; }, 1800);
                return;
            }
            throw new Error('Server returned ' + response.status);
        }

        const data = await response.json();

        if (data.success) {
            // ── Update the status badge ────────────────────────────────────
            const badge = document.getElementById('status-badge-' + id);
            if (badge) {
                badge.textContent = 'Contacted';
                badge.className   = 'status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400';
            }

            // ── Update the button ──────────────────────────────────────────
            btn.textContent = '✓ Contacted';
            btn.disabled    = true;
            btn.className   = btn.className
                .replace(/bg-amber-\S+/g, 'bg-emerald-100')
                .replace(/text-amber-\S+/g, 'text-emerald-700')
                .replace(/border-amber-\S+/g, 'border-emerald-200')
                .replace(/hover:bg-amber-\S+/g, 'hover:bg-emerald-100');

            // ── Update dashboard stats (new in v2) ────────────────────────
            if (data.stats) {
                updateStatCounters(data.stats);
            }

            showToast('Lead marked as Contacted successfully.', 'success');

        } else {
            // API returned success:false
            btn.disabled    = false;
            btn.textContent = 'Mark as Contacted';
            showToast(data.message || 'Update failed. Please try again.', 'error');
        }

    } catch (err) {
        console.error('[BizPulse] Status update error:', err);
        btn.disabled    = false;
        btn.textContent = 'Mark as Contacted';
        showToast('Network error. Please check your connection.', 'error');
    }
}
