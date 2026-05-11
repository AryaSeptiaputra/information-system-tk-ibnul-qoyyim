<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value
            || '';

        function closeAllActionMenus() {
            document.querySelectorAll('[data-action-menu]').forEach(menu => {
                menu.setAttribute('hidden', '');
            });
        }

        function openModal(modal) {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            const modalContent = modal.querySelector('.modal');
            if (modalContent) {
                modalContent.style.animation = 'none';
                setTimeout(() => {
                    modalContent.style.animation = 'modalFadeIn 0.3s ease-in-out';
                }, 10);
            }
        }

        function closeModal(modal) {
            const modalContent = modal.querySelector('.modal');
            if (modalContent) {
                modalContent.style.animation = 'modalFadeOut 0.3s ease-in-out';
            }

            setTimeout(() => {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');

                const anotherModalOpen = Array.from(document.querySelectorAll('.modal-overlay'))
                    .some(m => m !== modal && m.getAttribute('aria-hidden') === 'false' && !m.hidden);

                if (!anotherModalOpen) {
                    document.body.style.overflow = 'auto';
                }
            }, 300);
        }

        function wireModal(modal) {
            if (!modal || modal.dataset?.wired === '1') return;
            modal.dataset.wired = '1';

            modal.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal(modal);
                });
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });

            modal.querySelectorAll('form.admin-modal-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    if (!url) {
                        alert('Form action tidak ditemukan.');
                        return;
                    }

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                        }
                    })
                        .then(async (response) => {
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                const message = data?.message
                                    || (data?.errors ? Object.values(data.errors).flat()[0] : null)
                                    || 'Gagal menyimpan data.';
                                throw new Error(message);
                            }
                            return data;
                        })
                        .then((data) => {
                            if (data?.success) {
                                alert(data.message || 'Berhasil.');
                                location.reload();
                                return;
                            }

                            alert(data?.message || 'Gagal menyimpan data.');
                        })
                        .catch((err) => {
                            alert(err?.message || 'Terjadi kesalahan saat menyimpan data.');
                        });
                });
            });

            // Toggle reject reason field (used in registration detail modal)
            modal.querySelectorAll('form.admin-modal-form').forEach(form => {
                const statusSelect = form.querySelector('[data-reject-reason-toggle]');
                const reasonGroup = form.querySelector('[data-reject-reason-group]');
                const reasonInput = reasonGroup?.querySelector('input[name="reject_reason"], textarea[name="reject_reason"]');

                if (!statusSelect || !reasonGroup) return;

                function syncRejectReasonVisibility() {
                    const isRejected = statusSelect.value === 'rejected';
                    reasonGroup.hidden = !isRejected;
                    if (reasonInput) {
                        reasonInput.required = isRejected;
                        if (!isRejected) reasonInput.value = '';
                    }
                }

                statusSelect.addEventListener('change', syncRejectReasonVisibility);
                syncRejectReasonVisibility();
            });

            wireTeacherHonorCalculator(modal);
            wirePaymentFeeEditor(modal);
        }

        function wirePaymentFeeEditor(modal) {
            if (!modal || modal.dataset?.modalType !== 'payment') return;

            function toInt(value, fallback = 0) {
                const n = parseInt((value ?? '').toString(), 10);
                return Number.isFinite(n) ? n : fallback;
            }

            function toFloat(value, fallback = 0) {
                const n = parseFloat((value ?? '').toString());
                return Number.isFinite(n) ? n : fallback;
            }

            function formatRupiah(num) {
                const n = Math.round((num ?? 0) * 100) / 100;
                const asInt = Math.abs(n - Math.round(n)) < 0.000001;
                const value = asInt ? Math.round(n).toString() : n.toFixed(2);
                const parts = value.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return 'Rp ' + parts.join(',');
            }

            function safeParseArray(text) {
                const raw = (text ?? '').toString().trim();
                if (!raw) return [];
                try {
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch {
                    return [];
                }
            }

            modal.querySelectorAll('[data-fee-editor]').forEach(editor => {
                if (editor.dataset?.wired === '1') return;
                editor.dataset.wired = '1';

                const rowsEl = editor.querySelector('[data-fee-rows]');
                const jsonInput = editor.querySelector('[data-fee-json]');
                const jsonView = editor.querySelector('[data-fee-json-view]');
                const addBtn = editor.querySelector('[data-fee-add]');
                const totalEl = editor.querySelector('[data-fee-total]');
                const tpl = editor.querySelector('template[data-fee-row-template]');

                if (!rowsEl || !jsonInput || !tpl) return;

                function buildJsonFromRows() {
                    const result = [];

                    rowsEl.querySelectorAll('[data-fee-row]').forEach(row => {
                        const label = (row.querySelector('[data-fee-label]')?.value ?? '').toString().trim();
                        const qtyRaw = row.querySelector('[data-fee-qty]')?.value;
                        const amountRaw = row.querySelector('[data-fee-amount]')?.value;

                        const qty = Math.max(1, toInt(qtyRaw, 1));
                        const amount = Math.max(0, toFloat(amountRaw, 0));

                        const isEmpty = label === '' && (amountRaw === null || amountRaw === undefined || amountRaw === '' || amount === 0);
                        if (isEmpty) return;

                        result.push({
                            label: label !== '' ? label : 'Komponen',
                            amount,
                            qty,
                        });
                    });

                    return result;
                }

                function sync() {
                    const rows = buildJsonFromRows();
                    const json = rows.length ? JSON.stringify(rows) : '';
                    jsonInput.value = json;

                    if (jsonView) {
                        jsonView.value = rows.length ? JSON.stringify(rows, null, 2) : '';
                    }

                    const total = rows.reduce((sum, r) => sum + (toFloat(r.amount, 0) * Math.max(1, toInt(r.qty, 1))), 0);
                    if (totalEl) totalEl.textContent = formatRupiah(total);
                }

                function wireRow(rowEl) {
                    if (!rowEl) return;
                    rowEl.querySelectorAll('input').forEach(input => {
                        input.addEventListener('input', sync);
                        input.addEventListener('change', sync);
                    });

                    const removeBtn = rowEl.querySelector('[data-fee-remove]');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', () => {
                            rowEl.remove();
                            if (!rowsEl.querySelector('[data-fee-row]')) {
                                addRow({});
                            }
                            sync();
                        });
                    }
                }

                function addRow(data = {}) {
                    const fragment = tpl.content.cloneNode(true);
                    const row = fragment.querySelector('[data-fee-row]');
                    if (!row) return;

                    const labelInput = row.querySelector('[data-fee-label]');
                    const qtyInput = row.querySelector('[data-fee-qty]');
                    const amountInput = row.querySelector('[data-fee-amount]');

                    if (labelInput) labelInput.value = (data?.label ?? '').toString();
                    if (qtyInput) qtyInput.value = Math.max(1, toInt(data?.qty, 1)).toString();
                    if (amountInput) amountInput.value = (data?.amount ?? '').toString();

                    rowsEl.appendChild(fragment);

                    // Because fragment was appended, re-select the last row for wiring.
                    const lastRow = rowsEl.querySelectorAll('[data-fee-row]');
                    wireRow(lastRow[lastRow.length - 1]);
                }

                const initialRows = safeParseArray(jsonInput.value);
                if (initialRows.length) {
                    initialRows.forEach(r => addRow(r));
                } else {
                    addRow({});
                }

                if (addBtn) {
                    addBtn.addEventListener('click', () => {
                        addRow({});
                        sync();
                    });
                }

                sync();
            });
        }

        function wireTeacherHonorCalculator(modal) {
            if (!modal || modal.dataset?.modalType !== 'teacher-honor') return;

            const form = modal.querySelector('form.admin-modal-form');
            if (!form) return;

            const isEdit = !!form.querySelector('input[name="_method"][value="PUT"]');

            const teacherSelect = form.querySelector('#form-id-teacher');
            const teacherHidden = form.querySelector('input[name="id_teacher"]');
            const periodStartInput = form.querySelector('#form-period-start');
            const periodEndInput = form.querySelector('#form-period-end');

            const hadirInput = form.querySelector('#form-attendance-count');
            const izinInput = form.querySelector('#form-permission-count');
            const sakitInput = form.querySelector('#form-sickness-count');
            const alpaInput = form.querySelector('#form-absence-count');

            const rateInput = form.querySelector('#form-rate-per-attendance');
            const allowanceInput = form.querySelector('#form-allowance-total');
            const manualAdjustmentInput = form.querySelector('#form-manual-adjustment');
            const amountInput = form.querySelector('#form-amount');

            const attendancePreview = form.querySelector('[data-honor-attendance-preview]');
            const totalPreview = form.querySelector('[data-honor-total-preview]');

            const recapCard = form.querySelector('[data-honor-recap-card]');
            const recapPeriod = form.querySelector('[data-honor-recap-period]');
            const recapHadir = form.querySelector('[data-honor-recap-hadir]');
            const recapIzin = form.querySelector('[data-honor-recap-izin]');
            const recapSakit = form.querySelector('[data-honor-recap-sakit]');
            const recapAlpa = form.querySelector('[data-honor-recap-alpa]');
            const recapTotal = form.querySelector('[data-honor-recap-total]');
            const recapRate = form.querySelector('[data-honor-recap-rate]');
            const recapAllowance = form.querySelector('[data-honor-recap-allowance]');
            const recapAmount = form.querySelector('[data-honor-recap-amount]');
            const recapNote = form.querySelector('[data-honor-recap-note]');

            function getTeacherId() {
                return (teacherSelect?.value || teacherHidden?.value || '').toString();
            }

            function toInt(value) {
                const n = parseInt((value ?? '').toString(), 10);
                return Number.isFinite(n) && n >= 0 ? n : 0;
            }

            function toFloat(value) {
                const n = parseFloat((value ?? '').toString());
                return Number.isFinite(n) ? n : 0;
            }

            function formatRupiah(num) {
                const n = Math.round((num ?? 0) * 100) / 100;
                const asInt = Math.abs(n - Math.round(n)) < 0.000001;
                const value = asInt ? Math.round(n).toString() : n.toFixed(2);
                const parts = value.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return 'Rp ' + parts.join(',');
            }

            function getPeriodText() {
                const start = (periodStartInput?.value ?? '').toString();
                const end = (periodEndInput?.value ?? '').toString();
                if (!start || !end) return '-';
                return `${start} s/d ${end}`;
            }

            function updateRecapCard(data, opts = {}) {
                if (!recapCard) return;

                if (recapPeriod) recapPeriod.textContent = getPeriodText();

                const hadir = data ? toInt(data.hadir) : 0;
                const izin = data ? toInt(data.izin) : 0;
                const sakit = data ? toInt(data.sakit) : 0;
                const alpa = data ? toInt(data.alpa) : 0;
                const hasTotal = !!(data && data.total !== undefined && data.total !== null && data.total !== '');
                const total = hasTotal ? toInt(data.total) : (hadir + izin + sakit + alpa);

                if (recapHadir) recapHadir.textContent = hadir;
                if (recapIzin) recapIzin.textContent = izin;
                if (recapSakit) recapSakit.textContent = sakit;
                if (recapAlpa) recapAlpa.textContent = alpa;
                if (recapTotal) recapTotal.textContent = total;

                const rate = toFloat(rateInput?.value);
                const allowance = toFloat(allowanceInput?.value);
                const manual = toFloat(manualAdjustmentInput?.value);
                const estimated = (hadir * rate) + allowance + manual;
                if (recapRate) recapRate.textContent = formatRupiah(rate);
                if (recapAllowance) recapAllowance.textContent = formatRupiah(allowance);
                if (recapAmount) recapAmount.textContent = formatRupiah(estimated);

                if (recapNote) {
                    if (opts.loading) {
                        recapNote.textContent = 'Memuat rekap absensi...';
                    } else if (opts.message) {
                        recapNote.textContent = opts.message;
                    } else {
                        recapNote.textContent = 'Rekap berdasarkan absensi pada periode terpilih.';
                    }
                }
            }

            function computeTotal() {
                if (!hadirInput || !rateInput || !amountInput) return;
                const hadir = toInt(hadirInput.value);
                const rate = toFloat(rateInput.value);
                const allowance = toFloat(allowanceInput?.value);
                const manual = toFloat(manualAdjustmentInput?.value);
                const total = (hadir * rate) + allowance + manual;

                amountInput.value = (Math.round(total * 100) / 100).toFixed(2);
                if (totalPreview) {
                    totalPreview.textContent = `Preview total honor: ${hadir} × ${rate} + ${allowance} + ${manual} = ${formatRupiah(total)}`;
                }

                // Keep recap card's estimated total in sync with current inputs
                if (recapCard && recapAmount) {
                    recapAmount.textContent = formatRupiah(total);
                }
            }

            function updateAttendancePreview(data) {
                if (!data) {
                    if (attendancePreview) {
                        attendancePreview.textContent = 'Rekap absensi: -';
                    }
                    updateRecapCard(null, { message: 'Tidak ada data absensi pada periode terpilih.' });
                    return;
                }

                const hadir = toInt(data.hadir);
                const izin = toInt(data.izin);
                const sakit = toInt(data.sakit);
                const alpa = toInt(data.alpa);
                const rate = toFloat(data.rate);
                const allowanceTotal = toFloat(data.allowance_total);

                if (attendancePreview) {
                    attendancePreview.textContent = `Rekap absensi: Hadir ${hadir}, Izin ${izin}, Sakit ${sakit}, Alpa ${alpa}`;
                }

                if (rateInput) rateInput.value = rate.toFixed(2);
                if (allowanceInput) allowanceInput.value = allowanceTotal.toFixed(2);

                updateRecapCard({ hadir, izin, sakit, alpa, total: toInt(data.total) });
            }

            function applyAttendanceToInputs(data) {
                if (!data) return;
                if (hadirInput) hadirInput.value = toInt(data.hadir);
                if (izinInput) izinInput.value = toInt(data.izin);
                if (sakitInput) sakitInput.value = toInt(data.sakit);
                if (alpaInput) alpaInput.value = toInt(data.alpa);
            }

            function fetchAttendanceSummary() {
                const teacherId = getTeacherId();
                const periodStart = periodStartInput?.value;
                const periodEnd = periodEndInput?.value;

                if (!teacherId || !periodStart || !periodEnd) {
                    if (attendancePreview) attendancePreview.textContent = 'Rekap absensi: -';
                    updateRecapCard(null, { message: 'Pilih guru + periode untuk melihat rekap.' });
                    computeTotal();
                    return;
                }

                updateRecapCard(null, { loading: true });

                const url = `/admin/teacher-honors/attendance-summary?id_teacher=${encodeURIComponent(teacherId)}&period_start=${encodeURIComponent(periodStart)}&period_end=${encodeURIComponent(periodEnd)}`;

                fetch(url, {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                    }
                })
                    .then(async (response) => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok || !data?.success) {
                            throw new Error(data?.message || 'Gagal memuat rekap absensi.');
                        }
                        return data?.data;
                    })
                    .then((data) => {
                        updateAttendancePreview(data);
                        if (!isEdit) {
                            applyAttendanceToInputs(data);
                        }
                        computeTotal();
                    })
                    .catch(() => {
                        if (attendancePreview) attendancePreview.textContent = 'Rekap absensi: -';
                        updateRecapCard(null, { message: 'Gagal memuat rekap absensi.' });
                        computeTotal();
                    });
            }

            [teacherSelect, periodStartInput, periodEndInput].forEach(el => {
                if (!el) return;
                el.addEventListener('change', fetchAttendanceSummary);
                el.addEventListener('input', fetchAttendanceSummary);
            });

            [hadirInput, rateInput, allowanceInput, manualAdjustmentInput].forEach(el => {
                if (!el) return;
                el.addEventListener('input', computeTotal);
                el.addEventListener('change', computeTotal);
            });

            // Initialize
            fetchAttendanceSummary();
        }

        function upsertModalHtml(html) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newModal = wrapper.firstElementChild;
            if (!newModal || !newModal.id) return null;

            const existing = document.getElementById(newModal.id);
            if (existing) {
                existing.replaceWith(newModal);
            } else {
                document.body.appendChild(newModal);
            }

            wireModal(newModal);
            return newModal;
        }

        function fetchModalView(url) {
            const separator = url.includes('?') ? '&' : '?';
            const urlWithTs = `${url}${separator}_=${Date.now()}`;

            return fetch(urlWithTs, {
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                }
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal memuat form.');
                    }
                    return data;
                })
                .then((data) => data?.view);
        }

        function getAdminTypeFromModalId(modalId) {
            if (!modalId) return null;
            if (modalId.includes('student-payment')) return 'student-payment';
            if (modalId.includes('payment')) return 'payment';
            if (modalId.includes('student-attendance')) return 'student-attendance';
            if (modalId.includes('teacher-attendance')) return 'teacher-attendance';
            if (modalId.includes('teacher-honor')) return 'teacher-honor';
            if (modalId.includes('teacher-attendance-rate')) return 'teacher-attendance-rate';
            if (modalId.includes('teacher-position')) return 'teacher-position';
            if (modalId.includes('position-allowance')) return 'position-allowance';
            if (modalId.includes('allowance-type')) return 'allowance-type';
            if (modalId.includes('position')) return 'position';
            if (modalId.includes('facility')) return 'facility';
            if (modalId.includes('user')) return 'user';
            if (modalId.includes('teacher')) return 'teacher';
            if (modalId.includes('registration')) return 'registration';
            if (modalId.includes('parent')) return 'parent';
            if (modalId.includes('student')) return 'student';
            if (modalId.includes('class')) return 'class';
            return null;
        }

        function getEndpointForAdminType(type) {
            switch (type) {
                case 'user': return 'users';
                case 'teacher': return 'teachers';
                case 'registration': return 'registrations';
                case 'parent': return 'parents';
                case 'student': return 'students';
                case 'class': return 'classes';
                case 'student-attendance': return 'student-attendance';
                case 'teacher-attendance': return 'teacher-attendance';
                case 'teacher-honor': return 'teacher-honors';
                case 'position': return 'positions';
                case 'teacher-position': return 'teacher-positions';
                case 'allowance-type': return 'allowance-types';
                case 'position-allowance': return 'position-allowances';
                case 'teacher-attendance-rate': return 'teacher-attendance-rates';
                case 'facility': return 'facilities';
                case 'payment': return 'payments';
                case 'student-payment': return 'student-payments';
                default: return null;
            }
        }

        function getDeleteItemLabel(type) {
            switch (type) {
                case 'user': return 'pengguna';
                case 'teacher': return 'guru';
                case 'registration': return 'pendaftaran';
                case 'parent': return 'orang tua';
                case 'student': return 'murid';
                case 'class': return 'kelas';
                case 'student-attendance': return 'absensi murid';
                case 'teacher-attendance': return 'absensi guru';
                case 'teacher-honor': return 'honor guru';
                case 'position': return 'posisi guru';
                case 'teacher-position': return 'penugasan posisi';
                case 'allowance-type': return 'jenis tunjangan';
                case 'position-allowance': return 'tunjangan posisi';
                case 'teacher-attendance-rate': return 'tarif kehadiran';
                case 'facility': return 'fasilitas';
                case 'payment': return 'payment';
                case 'student-payment': return 'tagihan murid';
                default: return 'data';
            }
        }

        // ===== ACTION DROPDOWN MENU =====
        document.querySelectorAll('[data-action-menu-toggle]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                if (!menu) return;

                const isHidden = menu.hasAttribute('hidden');
                closeAllActionMenus();

                if (isHidden) {
                    menu.removeAttribute('hidden');
                }
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('[data-action-menu-toggle]')) {
                closeAllActionMenus();
            }
        });

        // ===== ITEMS PER PAGE CHANGE =====
        document.querySelectorAll('.admin-per-page-select').forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form')?.submit();
            });
        });

        // ===== DELETE CONFIRMATION (delegated; supports dynamically injected modals) =====
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('[data-confirm-delete]');
            if (!deleteBtn) return;

            e.preventDefault();

            const itemName = deleteBtn.getAttribute('data-item-name');
            const itemId = deleteBtn.getAttribute('data-item-id');
            const deleteType = deleteBtn.getAttribute('data-delete-type');

            const endpoint = getEndpointForAdminType(deleteType);
            const itemType = getDeleteItemLabel(deleteType);

            if (!endpoint) {
                alert('Tipe data tidak dikenali.');
                return;
            }

            if (!confirm(`Apakah Anda yakin ingin menghapus ${itemType} "${itemName}"?`)) {
                return;
            }

            const url = `/admin/${endpoint}/${itemId}`;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                }
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data.');
                    }
                    return data;
                })
                .then((data) => {
                    if (data?.success) {
                        alert(data.message || 'Berhasil dihapus.');
                        location.reload();
                        return;
                    }
                    alert(data?.message || 'Gagal menghapus data.');
                })
                .catch((err) => {
                    alert(err?.message || 'Terjadi kesalahan saat menghapus data.');
                });
        });

        // ===== MODAL OPEN (AJAX LOAD, delegated; supports dynamically injected modals) =====
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-modal-open]');
            if (!trigger) return;

            e.preventDefault();
            closeAllActionMenus();

            // Allow opening a modal via an explicit URL (for non-CRUD actions)
            const modalUrl = trigger.getAttribute('data-modal-url');
            const explicitItemId = trigger.getAttribute('data-item-id');

            // If clicked from inside an open modal overlay, close it first
            const parentOverlay = trigger.closest('.modal-overlay');
            if (parentOverlay && parentOverlay.getAttribute('aria-hidden') === 'false') {
                closeModal(parentOverlay);
            }

            if (modalUrl) {
                const resolvedUrl = explicitItemId
                    ? modalUrl.replaceAll('{id}', explicitItemId).replaceAll(':id', explicitItemId)
                    : modalUrl;

                fetchModalView(resolvedUrl)
                    .then((html) => {
                        if (!html) throw new Error('Form tidak tersedia.');

                        const modal = upsertModalHtml(html);
                        if (!modal) throw new Error('Gagal memuat modal.');

                        openModal(modal);
                    })
                    .catch((err) => {
                        alert(err?.message || 'Terjadi kesalahan saat memuat form.');
                    });

                return;
            }

            const modalId = trigger.getAttribute('data-modal-open');
            const itemId = trigger.getAttribute('data-item-id');
            const type = getAdminTypeFromModalId(modalId);

            if (!modalId || !type) return;

            const endpoint = getEndpointForAdminType(type);
            if (!endpoint) return;

            const isEdit = modalId.startsWith('edit-');
            const isView = modalId.startsWith('view-');

            const url = isEdit
                ? `/admin/${endpoint}/${itemId}/edit`
                : (isView
                    ? `/admin/${endpoint}/${itemId}`
                    : `/admin/${endpoint}/create`);

            fetchModalView(url)
                .then((html) => {
                    if (!html) throw new Error('Form tidak tersedia.');

                    const modal = upsertModalHtml(html);
                    if (!modal) throw new Error('Gagal memuat modal.');

                    openModal(modal);
                })
                .catch((err) => {
                    alert(err?.message || 'Terjadi kesalahan saat memuat form.');
                });
        });

        // Wire any modals already present
        document.querySelectorAll('.modal-overlay').forEach(modal => wireModal(modal));
    });
</script>
