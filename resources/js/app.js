import './bootstrap';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

window.Alpine = Alpine;
Alpine.start();

Chart.register(...registerables);
window.Chart = Chart;

/**
 * Utility global untuk load form ke dalam <x-ui.modal>.
 * Fetch endpoint AJAX (return JSON {view: html}), extract <form>, inject ke container,
 * dispatch open-modal event ke Alpine, lalu attach submit handler agar submit jadi AJAX.
 *
 * Pemakaian:
 *   <button onclick="loadFormIntoModal('/admin/positions/create', 'form-modal-content', 'form-modal')">
 *
 * Setelah submit sukses, default behavior = location.reload().
 * Override via opsi: loadFormIntoModal(url, containerId, modalName, { onSuccess: (data) => ... })
 */
window.loadFormIntoModal = async function (url, containerId, modalName, opts = {}) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`[loadFormIntoModal] container #${containerId} tidak ditemukan`);
        return;
    }
    container.innerHTML = '<p class="ui-stat-card__hint">Memuat...</p>';
    window.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));

    try {
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            credentials: 'same-origin',
        });
        const text = await res.text();

        if (!res.ok) {
            container.innerHTML = `<div class="ui-form-error">
                <strong>HTTP ${res.status} ${res.statusText}</strong>
                <pre style="white-space:pre-wrap;font-size:11px;margin-top:8px;max-height:240px;overflow:auto;">${escapeHtml(text.substring(0, 1500))}</pre>
            </div>`;
            return;
        }

        let data;
        try { data = JSON.parse(text); } catch (e) {
            container.innerHTML = `<div class="ui-form-error">
                <strong>Response bukan JSON valid.</strong>
                <pre style="white-space:pre-wrap;font-size:11px;margin-top:8px;max-height:240px;overflow:auto;">${escapeHtml(text.substring(0, 1500))}</pre>
            </div>`;
            return;
        }

        // Parse view HTML, ambil hanya <form>-nya (skip wrapper modal-overlay lama yang punya attr `hidden`).
        const tempDoc = new DOMParser().parseFromString(data.view || '', 'text/html');
        const form = tempDoc.querySelector('form.admin-modal-form') || tempDoc.querySelector('form');

        if (!form) {
            container.innerHTML = '<p class="ui-form-error">Form tidak ditemukan di response.</p>';
            return;
        }

        // Bersihkan inline class "hidden" yang inherit dari wrapper lama, lalu inject.
        form.removeAttribute('hidden');
        container.innerHTML = '';
        container.appendChild(form);

        // Attach AJAX submit handler (controller existing return JSON {success, message}).
        attachAjaxSubmitHandler(form, modalName, opts);
    } catch (err) {
        container.innerHTML = `<div class="ui-form-error">
            <strong>Network error:</strong> ${escapeHtml(err.message)}
        </div>`;
    }
};

function attachAjaxSubmitHandler(form, modalName, opts) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const formData = new FormData(form);
        const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();

        try {
            const res = await fetch(form.action, {
                method: method === 'GET' ? 'GET' : 'POST', // Laravel pakai POST + _method override
                body: method === 'GET' ? null : formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
            });

            const text = await res.text();
            let data = null;
            try { data = JSON.parse(text); } catch (_) {}

            if (res.ok && data?.success) {
                if (opts.onSuccess) {
                    opts.onSuccess(data);
                } else {
                    // Default: reload halaman dengan flash success di session
                    window.location.reload();
                }
                return;
            }

            // Tampilkan error di atas form
            const errMsg = data?.message
                ? `<strong>${escapeHtml(data.message)}</strong>`
                : `<strong>HTTP ${res.status}</strong>`;
            const errors = data?.errors
                ? Object.values(data.errors).flat().map(e => `<li>${escapeHtml(e)}</li>`).join('')
                : '';

            let banner = form.querySelector('.ui-form-ajax-error');
            if (!banner) {
                banner = document.createElement('div');
                banner.className = 'ui-form-error ui-form-ajax-error';
                banner.style.marginBottom = '12px';
                form.insertBefore(banner, form.firstChild);
            }
            banner.innerHTML = errMsg + (errors ? `<ul style="margin:8px 0 0 16px;">${errors}</ul>` : '');
        } catch (err) {
            console.error('[ajax submit]', err);
            alert('Network error: ' + err.message);
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}

/**
 * Utility untuk load detail (read-only) ke <x-ui.modal>.
 * Mirip loadFormIntoModal tapi inject .modal-body (atau .modal-admin content)
 * tanpa form submit handler.
 */
window.loadDetailIntoModal = async function (url, containerId, modalName) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`[loadDetailIntoModal] container #${containerId} tidak ditemukan`);
        return;
    }
    container.innerHTML = '<p class="ui-stat-card__hint">Memuat...</p>';
    window.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));

    try {
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            credentials: 'same-origin',
        });
        const text = await res.text();

        if (!res.ok) {
            container.innerHTML = `<div class="ui-form-error">
                <strong>HTTP ${res.status}</strong>
                <pre style="white-space:pre-wrap;font-size:11px;margin-top:8px;max-height:240px;overflow:auto;">${escapeHtml(text.substring(0, 1500))}</pre>
            </div>`;
            return;
        }

        let data;
        try { data = JSON.parse(text); } catch (e) {
            container.innerHTML = `<div class="ui-form-error">Response bukan JSON.</div>`;
            return;
        }

        const tempDoc = new DOMParser().parseFromString(data.view || '', 'text/html');
        // Coba ambil .modal-body, fallback ke seluruh modal-admin content tanpa header.
        const body = tempDoc.querySelector('.modal-body')
            || tempDoc.querySelector('.modal-admin')
            || tempDoc.querySelector('.modal-overlay');

        if (!body) {
            container.innerHTML = data.view || '<p>Content kosong.</p>';
            return;
        }

        // Bersihkan attribut hidden inherit
        body.removeAttribute('hidden');
        body.querySelectorAll('[hidden]').forEach(el => {
            // Hapus hidden hanya untuk elemen wrapper, bukan field validation yang mungkin perlu hidden
            if (el.classList.contains('modal-overlay') || el.classList.contains('modal-admin')) {
                el.removeAttribute('hidden');
            }
        });

        container.innerHTML = '';
        container.appendChild(body);
    } catch (err) {
        container.innerHTML = `<div class="ui-form-error"><strong>Network error:</strong> ${escapeHtml(err.message)}</div>`;
    }
};

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Tab switcher for profil
function switchTab(tabId) {
    // Remove active class from all buttons and content
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    // Add active class to clicked button and corresponding content
    document.querySelector(`[data-tab="${tabId}"]`)?.classList.add('active');
    document.getElementById(tabId)?.classList.add('active');
}

// Attach event listeners to tab buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
});

// Mobile menu toggle
function toggleMenu() {
    const nav = document.querySelector('.nav-links');
    if (nav.style.display === 'flex') {
        nav.style.display = '';
    } else {
        nav.style.cssText = 'display:flex;flex-direction:column;position:fixed;top:70px;left:0;right:0;background:white;padding:20px 5%;border-bottom:3px solid var(--green-light);z-index:999;gap:4px;box-shadow:0 10px 30px rgba(0,0,0,0.1)';
    }
}

// Scroll reveal animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
        if (e.isIntersecting) {
            setTimeout(() => e.target.classList.add('visible'), i * 80);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
