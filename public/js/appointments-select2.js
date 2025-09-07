// appointments-select2.js
// Minimal, dependency: jQuery + Select2 (official). Integrates with Livewire v3 via window.dispatchEvent

(function () {
    'use strict';

    // API endpoints removed. This script now prefers server-rendered <option> elements
    // and will only use AJAX if a data-url attribute is present on the select element.
    const DOCTOR_PLACES_URL = null;
    const PATIENTS_URL = null; // kept for backward compatibility when a data attribute is used

    function normalizeResults(data) {
        if (!data) return [];
        if (Array.isArray(data)) return data;
        if (Array.isArray(data.results)) return data.results;
        if (Array.isArray(data.data)) return data.data;
        return [];
    }

    function destroyIfExists($el) {
        if ($el && $el.length && $el.hasClass('select2-hidden-accessible')) {
            try { $el.select2('destroy'); } catch (e) { /* ignore */ }
        }
    }

    function initAjaxSelect($el, url, placeholder, minInput = 1) {
        if (!($el && $el.length)) return;
        destroyIfExists($el);

        // Allow optional data attribute to control dropdown parent when needed
        // e.g. <select data-select2-parent="#someContainer"> to avoid clipping inside overflowed parents
        const parentSelector = $el.data('select2-parent');
        const dropdownParent = parentSelector ? $(parentSelector) : undefined;

        // Determine if select should be multi-select: either the element already has multiple
        // or consumer set data-select2-multiple="true"
        const dataMultiple = $el.data('select2-multiple');
        const isMultiple = $el.prop('multiple') || dataMultiple === true || dataMultiple === 'true';
        if (isMultiple) {
            // ensure native multiple attribute exists for Select2 to render tags properly
            try { $el.prop('multiple', true); } catch (e) { /* ignore */ }
        }

        $el.select2({
            placeholder: placeholder || 'Buscar...',
            allowClear: true,
            minimumInputLength: minInput,
            closeOnSelect: !isMultiple,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (data) {
                    const items = normalizeResults(data).map(item => {
                        if (!item) return null;
                        if (item.id !== undefined) return item;
                        return { id: item.value || item.key || item.id || item, text: item.text || item.name || item.label || String(item) };
                    }).filter(Boolean);
                    return { results: items };
                }
            },
            width: '100%',
            // apply dropdownParent only when provided to avoid changing default behavior
            ...(dropdownParent ? { dropdownParent: dropdownParent } : {}),
        // for multi-select UX nice to show tags; Select2 uses the multiple attr to decide
        // keep placeholder behavior consistent
        });

        // Robust autofocus: try immediately, then with a small timeout and animation frame
        $el.on('select2:open', function () {
            function focusSearch() {
                const f = document.querySelector('.select2-search__field');
                if (f) {
                    try { f.focus(); f.select && f.select(); } catch (e) { /* ignore */ }
                    return true;
                }
                return false;
            }

            // first try
            if (focusSearch()) return;

            // try again on next frame(s) for robustness
            let attempts = 0;
            const maxAttempts = 6;
            const tryFocus = function () {
                attempts++;
                if (focusSearch() || attempts >= maxAttempts) return;
                requestAnimationFrame(tryFocus);
            };
            setTimeout(() => requestAnimationFrame(tryFocus), 20);
        });
    }

    function initDoctorPlaceSelect() {
        const $s = $('#doctor-place-select');
        if (!$s.length) return;

        // If consumer specified a data-url on the select, use AJAX; otherwise use DOM options
        const ajaxUrl = $s.data('url') || DOCTOR_PLACES_URL;
        if (ajaxUrl) {
            initAjaxSelect($s, ajaxUrl, 'Buscar consultorio (doctor - consultorio)...', 1);
        } else {
            // Initialize Select2 using existing DOM options
            $s.select2({ placeholder: 'Buscar consultorio (doctor - consultorio)...', allowClear: true, closeOnSelect: false, width: '100%', matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                const term = params.term.toUpperCase();
                if (typeof data.children !== 'undefined') {
                    const filteredChildren = [];
                    $.each(data.children, function (idx, child) { if (child.text && child.text.toUpperCase().indexOf(term) === 0) filteredChildren.push(child); });
                    if (filteredChildren.length) { const modified = $.extend({}, data, true); modified.children = filteredChildren; return modified; }
                    return null;
                }
                if (data.text && data.text.toUpperCase().indexOf(term) === 0) return data;
                return null;
            }});
        }

        // Handle both select and unselect to build current selected ids array
        $s.off('select2:select.appointments select2:unselect.appointments').on('select2:select.appointments select2:unselect.appointments', function (e) {
            // current values may be a string or array depending on single/multi
            const raw = $s.val();
            const selected = Array.isArray(raw) ? raw.map(v => parseInt(v)).filter(Boolean) : (raw ? [parseInt(raw)] : []);

            // Dispatch Livewire event with array of selected ids
            window.dispatchEvent(new CustomEvent('livewire:dispatch', { detail: { name: 'doctorPlaceSelected', params: [selected] } }));
            try { window.dispatchEvent(new CustomEvent('doctorPlaceSelected', { detail: { ids: selected } })); } catch (e) { /* ignore */ }
            if (window.Livewire && typeof window.Livewire.emit === 'function') {
                try { window.Livewire.emit('doctorPlaceSelected', selected); } catch (e) { /* ignore */ }
            }

            // Preserve selection in case Livewire re-renders: ensure all selected options exist
            setTimeout(function () {
                try {
                    // If multiple, ensure each selected id has an option
                    if (Array.isArray(selected) && selected.length) {
                        selected.forEach(function (id) {
                            if (!$s.find('option[value="' + id + '"]').length) {
                                // try to find corresponding text from event (if available)
                                const data = e?.params?.data ?? null;
                                const text = data && data.id == id ? (data.text || (data.doctor_name && data.place_name ? data.doctor_name + ' - ' + data.place_name : String(id))) : String(id);
                                const option = new Option(text, id, true, true);
                                $s.append(option);
                            }
                        });
                        $s.val(selected.map(String));
                    } else if (selected.length === 1) {
                        const id = selected[0];
                        if (!$s.find('option[value="' + id + '"]').length) {
                            const data = e?.params?.data ?? null;
                            const text = data ? (data.text || String(id)) : String(id);
                            const option = new Option(text, id, true, true);
                            $s.append(option);
                        } else {
                            $s.val(String(id));
                        }
                    }
                    $s.trigger('change.select2');
                } catch (err) {
                    console.warn('preserve selection failed', err);
                }
            }, 150);
        });
    }

    function initFormDoctorSelect() {
        // Selects that use wire:model="doctor_medicaloffice_id" should also be enhanced
        const $sel = $("select[wire\\:model='doctor_medicaloffice_id']");
        if (!$sel.length) return;

    const formAjaxUrl = $sel.data('url') || DOCTOR_PLACES_URL;
    if (formAjaxUrl) initAjaxSelect($sel, formAjaxUrl, 'Seleccionar consultorio médico...', 0);

        // When user selects from this select, Livewire will receive the change via wire:model
        $sel.off('select2:select.formdoc').on('select2:select.formdoc', function (e) {
            // no-op: wire:model will sync
        });
    }

    function initPatientSelect() {
        const $p = $('#patient-select');
        if (!$p.length) return;
        // try primary patients endpoint, fallback to PATIENTS_URL
    const url = $p.data('url') || PATIENTS_URL;
    if (url) initAjaxSelect($p, url, 'Buscar paciente...', 1);

        $p.off('select2:select.patient').on('select2:select.patient', function (e) {
            const data = e?.params?.data ?? null;
            if (!data) return;
            const id = parseInt(data.id);
            window.dispatchEvent(new CustomEvent('livewire:dispatch', { detail: { name: 'patientSelected', params: [id] } }));
            try {
                window.dispatchEvent(new CustomEvent('patientSelected', { detail: { id: id } }));
            } catch (e) { /* ignore */ }
            if (window.Livewire && typeof window.Livewire.emit === 'function') {
                try { window.Livewire.emit('patientSelected', id); } catch (e) { /* ignore */ }
            }
        });
    }

    // Re-init all selects (useful after Livewire updates)
    function reinitAll() {
        initDoctorPlaceSelect();
        initFormDoctorSelect();
        initPatientSelect();
    }

    // Debounced scheduler for mutations
    let reinitTimeout = null;
    function scheduleReinit(delay = 250) {
        clearTimeout(reinitTimeout);
        reinitTimeout = setTimeout(reinitAll, delay);
    }

    // Observe DOM for newly added selects and reinit
    if (window.MutationObserver) {
        const observer = new MutationObserver(function (mutations) {
            for (const m of mutations) {
                if (m.addedNodes && m.addedNodes.length) {
                    for (const n of m.addedNodes) {
                        if (n.nodeType === 1) {
                            if (n.matches && (n.matches('#doctor-place-select') || n.matches('#patient-select') || n.querySelector && n.querySelector('#doctor-place-select, #patient-select, select[wire\\:model="doctor_medicaloffice_id"]'))) {
                                scheduleReinit();
                                return;
                            }
                        }
                    }
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Expose reinit for manual triggers
    window.select2AppointmentsReinit = reinitAll;

    // Init on DOM ready
    $(function () {
        try {
            reinitAll();
            // small delayed reinit for Livewire race conditions
            setTimeout(reinitAll, 350);
        } catch (e) {
            console.error('appointments-select2 init failed', e);
        }
    });

    // Reinit after Livewire updates (v3 event names)
    window.addEventListener('livewire:update', function () { scheduleReinit(100); });
    window.addEventListener('livewire:navigated', function () { scheduleReinit(100); });

})();
