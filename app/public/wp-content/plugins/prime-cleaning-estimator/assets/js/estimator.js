/**
 * Prime Cleaning Estimator - Client Controller
 * Pure Vanilla JavaScript (No React/Angular/jQuery dependencies)
 */

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('pce-estimator-container');
    if (!container) return;

    const form = document.getElementById('pce-estimator-form');
    if (!form) return; // The approved flow retains the historical anchor.
    const settings = window.pceData ? window.pceData.settings : {};
    const ajaxUrl = window.pceData ? window.pceData.ajax_url : '';
    const nonce = window.pceData ? window.pceData.nonce : '';
    const renderTs = window.pceData ? window.pceData.render_ts : Math.floor(Date.now() / 1000);

    let currentStep = 1;
    let cachedBreakdown = null;

    // Elements
    const stepPanels = form.querySelectorAll('.pce-step-panel');
    const stepNavItems = container.querySelectorAll('.pce-step-item');
    const suburbInput = document.getElementById('pce_suburb');
    const postcodeInput = document.getElementById('pce_postcode');
    const coverageBox = document.getElementById('pce-coverage-box');
    const serviceRadios = form.querySelectorAll('input[name="service"]');
    const residentialFields = document.getElementById('pce-residential-fields');
    const officeFields = document.getElementById('pce-office-fields');
    const bedroomsSelect = document.getElementById('pce_bedrooms');
    const bathroomsSelect = document.getElementById('pce_bathrooms');
    const frequencyPills = form.querySelectorAll('.pce-freq-pill');
    const officeHoursInput = document.getElementById('pce_office_hours');
    const officeFreqSelect = document.getElementById('pce_office_frequency');
    const submitBtn = document.getElementById('pce-submit-btn');
    const submitError = document.getElementById('pce-submit-error');

    // Step 1: Postcode & Coverage Checker
    function checkPostcodeCoverage(code) {
        code = (code || '').trim();
        if (!/^\d{4}$/.test(code)) {
            return { valid: false, covered: false };
        }
        const prefixes = (settings.postcode_prefixes || '314, 315')
            .split(',')
            .map(p => p.trim())
            .filter(p => p.length > 0);

        const covered = prefixes.some(p => code.startsWith(p));
        return { valid: true, covered: covered };
    }

    function updateCoverageUI() {
        const code = postcodeInput.value.trim();
        if (code.length === 0) {
            coverageBox.style.display = 'none';
            return;
        }

        const res = checkPostcodeCoverage(code);
        if (!res.valid) {
            coverageBox.className = 'pce-coverage-alert pce-coverage-out';
            coverageBox.innerHTML = '&#9888; Please enter a valid 4-digit Victorian postcode (e.g. 3145).';
            coverageBox.style.display = 'block';
            return;
        }

        if (res.covered) {
            coverageBox.className = 'pce-coverage-alert pce-coverage-covered';
            coverageBox.innerHTML = '&#10003; <strong>Great news!</strong> Postcode ' + escapeHtml(code) + ' is within our standard service area (Malvern East hub).';
            coverageBox.style.display = 'block';
        } else {
            coverageBox.className = 'pce-coverage-alert pce-coverage-out';
            coverageBox.innerHTML = '&#9432; <strong>Outside standard demo prefix area:</strong> Postcode ' + escapeHtml(code) + ' is outside prefixes (' + escapeHtml(settings.postcode_prefixes) + '). You may still proceed &mdash; our team will prepare a custom manual quote for you.';
            coverageBox.style.display = 'block';
        }
    }

    postcodeInput.addEventListener('input', updateCoverageUI);

    // Step 2: Service Selection UI
    serviceRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            form.querySelectorAll('.pce-radio-card').forEach(card => card.classList.remove('pce-selected'));
            const card = this.closest('.pce-radio-card');
            if (card) card.classList.add('pce-selected');
            syncServiceFields();
            calculateEstimate();
        });
    });

    function getSelectedService() {
        const checked = form.querySelector('input[name="service"]:checked');
        return checked ? checked.value : 'regular';
    }

    function syncServiceFields() {
        const service = getSelectedService();
        const step3Desc = document.getElementById('pce-step3-desc');
        const resFreqRow = document.getElementById('pce-res-frequency-row');
        const weeklyPill = document.getElementById('pce-pill-weekly');
        const fortnightlyPill = document.getElementById('pce-pill-fortnightly');

        if (service === 'office') {
            residentialFields.style.display = 'none';
            officeFields.style.display = 'block';
            step3Desc.textContent = 'Specify estimated service hours and recurrence for your office or commercial site.';
        } else {
            residentialFields.style.display = 'block';
            officeFields.style.display = 'none';

            if (service === 'regular') {
                step3Desc.textContent = 'Select bedroom and bathroom count, plus your preferred regular cleaning schedule.';
                resFreqRow.style.display = 'block';
                if (weeklyPill) weeklyPill.style.display = 'flex';
                if (fortnightlyPill) fortnightlyPill.style.display = 'flex';
            } else {
                step3Desc.textContent = 'Select bedroom and bathroom count. ' + (service === 'deep' ? 'Deep cleaning' : 'End-of-lease cleaning') + ' is conducted as a comprehensive one-off booking.';
                // Deep / Lease are single comprehensive cleans
                const onceRadio = form.querySelector('input[name="frequency"][value="once"]');
                if (onceRadio) {
                    onceRadio.checked = true;
                    frequencyPills.forEach(p => p.classList.remove('pce-freq-selected'));
                    onceRadio.closest('.pce-freq-pill').classList.add('pce-freq-selected');
                }
                if (weeklyPill) weeklyPill.style.display = 'none';
                if (fortnightlyPill) fortnightlyPill.style.display = 'none';
            }
        }
    }

    // Step 3: Frequency Pills
    frequencyPills.forEach(pill => {
        pill.addEventListener('click', function () {
            frequencyPills.forEach(p => p.classList.remove('pce-freq-selected'));
            this.classList.add('pce-freq-selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                calculateEstimate();
            }
        });
    });

    bedroomsSelect.addEventListener('change', calculateEstimate);
    bathroomsSelect.addEventListener('change', calculateEstimate);
    officeHoursInput.addEventListener('input', calculateEstimate);
    officeFreqSelect.addEventListener('change', calculateEstimate);

    // Calculation Engine
    function calculateEstimate() {
        const service = getSelectedService();
        const postcode = postcodeInput.value.trim();
        const suburb = suburbInput.value.trim();
        const cov = checkPostcodeCoverage(postcode);

        let bedrooms = parseInt(bedroomsSelect.value, 10) || 2;
        let bathrooms = parseInt(bathroomsSelect.value, 10) || 1;
        let frequency = 'once';
        let officeHours = parseFloat(officeHoursInput.value) || (settings.office_min_hours || 2.0);

        if (service === 'office') {
            frequency = officeFreqSelect.value;
        } else {
            const freqChecked = form.querySelector('input[name="frequency"]:checked');
            frequency = freqChecked ? freqChecked.value : 'once';
        }

        let breakdownRows = [];
        let rawSubtotal = 0;
        let discPct = 0;
        let discAmt = 0;
        let serviceMultiplier = 1.0;

        if (service === 'office') {
            const hourly = settings.office_hourly_rate || 55.0;
            rawSubtotal = officeHours * hourly;
            breakdownRows.push({
                label: 'Commercial Cleaning (' + officeHours + ' hrs @ $' + hourly.toFixed(2) + '/hr)',
                val: '$' + rawSubtotal.toFixed(2)
            });

            if (frequency === 'weekly') {
                discPct = settings.discount_weekly || 15.0;
            } else if (frequency === 'fortnightly') {
                discPct = settings.discount_fortnightly || 10.0;
            }
        } else {
            const baseRate = settings.base_regular || 80.0;
            const bedRate = settings.bedroom_rate || 25.0;
            const bathRate = settings.bathroom_rate || 35.0;

            const bedCost = bedrooms * bedRate;
            const bathCost = bathrooms * bathRate;
            const baseHome = baseRate + bedCost + bathCost;

            breakdownRows.push({ label: 'Base Callout Rate', val: '$' + baseRate.toFixed(2) });
            breakdownRows.push({ label: 'Bedrooms (' + bedrooms + ' @ $' + bedRate.toFixed(2) + ')', val: '$' + bedCost.toFixed(2) });
            breakdownRows.push({ label: 'Bathrooms (' + bathrooms + ' @ $' + bathRate.toFixed(2) + ')', val: '$' + bathCost.toFixed(2) });

            if (service === 'deep') {
                serviceMultiplier = settings.multiplier_deep || 1.5;
                breakdownRows.push({ label: 'Deep Cleaning Multiplier', val: '&times; ' + serviceMultiplier.toFixed(2) });
            } else if (service === 'lease') {
                serviceMultiplier = settings.multiplier_lease || 2.0;
                breakdownRows.push({ label: 'End-of-Lease Multiplier', val: '&times; ' + serviceMultiplier.toFixed(2) });
            }

            rawSubtotal = baseHome * serviceMultiplier;

            if (service === 'regular') {
                if (frequency === 'weekly') {
                    discPct = settings.discount_weekly || 15.0;
                } else if (frequency === 'fortnightly') {
                    discPct = settings.discount_fortnightly || 10.0;
                }
            }
        }

        breakdownRows.push({ label: 'Raw Subtotal', val: '$' + rawSubtotal.toFixed(2) });

        if (discPct > 0) {
            discAmt = (rawSubtotal * discPct) / 100;
            const freqLabel = frequency === 'weekly' ? 'Weekly schedule' : 'Fortnightly schedule';
            breakdownRows.push({
                label: 'Frequency Discount (' + freqLabel + ' -' + discPct + '%)',
                val: '-$' + discAmt.toFixed(2)
            });
        }

        let afterDisc = Math.max(0, rawSubtotal - discAmt);
        const minCharge = settings.minimum_charge || 100.0;
        let minApplied = false;

        if (afterDisc < minCharge) {
            minApplied = true;
            afterDisc = minCharge;
        }

        let taxNote = settings.tax_disclaimer || 'Demo pricing in AUD. Tax treatment subject to confirmation.';
        let finalTotal = afterDisc;

        if (settings.tax_mode === 'inclusive') {
            const gst = finalTotal / 11;
            taxNote = 'Includes 10% GST ($' + gst.toFixed(2) + ' AUD)';
        } else if (settings.tax_mode === 'exclusive') {
            const gst = finalTotal * 0.10;
            finalTotal += gst;
            taxNote = 'Plus 10% GST ($' + gst.toFixed(2) + ' AUD)';
        }

        cachedBreakdown = {
            service: service,
            serviceName: getServiceName(service),
            bedrooms: bedrooms,
            bathrooms: bathrooms,
            frequency: frequency,
            officeHours: officeHours,
            rawSubtotal: rawSubtotal,
            discountAmt: discAmt,
            minApplied: minApplied,
            finalTotal: finalTotal,
            taxNote: taxNote,
            postcode: postcode,
            suburb: suburb,
            covered: cov.covered,
            breakdownRows: breakdownRows
        };

        renderBreakdownUI(cachedBreakdown);
    }

    function renderBreakdownUI(b) {
        const totalDisplay = document.getElementById('pce-display-total');
        const taxDisplay = document.getElementById('pce-display-tax');
        const rowsContainer = document.getElementById('pce-breakdown-rows');
        const minNotice = document.getElementById('pce-min-charge-notice');
        const areaRecap = document.getElementById('pce-area-recap');

        totalDisplay.textContent = '$' + b.finalTotal.toFixed(2) + ' AUD';
        taxDisplay.textContent = b.taxNote;

        let rowsHtml = '';
        b.breakdownRows.forEach(r => {
            rowsHtml += '<tr><td>' + r.label + '</td><td>' + r.val + '</td></tr>';
        });
        rowsHtml += '<tr><td><strong>Indicative Estimate Total</strong></td><td><strong>$' + b.finalTotal.toFixed(2) + ' AUD</strong></td></tr>';
        rowsContainer.innerHTML = rowsHtml;

        if (b.minApplied) {
            minNotice.style.display = 'block';
        } else {
            minNotice.style.display = 'none';
        }

        let recapHtml = '<strong>Location:</strong> ' + (b.suburb ? escapeHtml(b.suburb) + ', ' : '') + 'VIC ' + escapeHtml(b.postcode);
        if (b.covered) {
            recapHtml += ' &mdash; <span style="color:#16a34a; font-weight:600;">Within Malvern East service area</span>';
        } else {
            recapHtml += ' &mdash; <span style="color:#ea580c; font-weight:600;">Outside automated service area (manual quote required)</span>';
        }
        areaRecap.innerHTML = recapHtml;
    }

    function getServiceName(s) {
        const map = {
            regular: 'Regular Home Cleaning',
            deep: 'Deep Cleaning',
            lease: 'End-of-Lease Cleaning',
            office: 'Commercial & Office Cleaning'
        };
        return map[s] || 'Cleaning Service';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Step Navigation Controller
    function goToStep(step) {
        if (step > currentStep) {
            if (!validateStep(currentStep)) {
                return;
            }
        }

        currentStep = step;
        stepPanels.forEach(panel => {
            if (parseInt(panel.getAttribute('data-panel'), 10) === step) {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });

        stepNavItems.forEach(item => {
            const itemStep = parseInt(item.getAttribute('data-step'), 10);
            item.classList.remove('pce-step-active', 'pce-step-done');
            if (itemStep === step) {
                item.classList.add('pce-step-active');
            } else if (itemStep < step) {
                item.classList.add('pce-step-done');
            }
        });

        if (step === 4) {
            calculateEstimate();
        } else if (step === 5) {
            calculateEstimate();
            populateRecap();
        }

        // Scroll smoothly to top of estimator container
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validateStep(step) {
        if (step === 1) {
            const suburb = suburbInput.value.trim();
            const code = postcodeInput.value.trim();
            if (!suburb) {
                alert('Please enter your suburb.');
                suburbInput.focus();
                return false;
            }
            if (!/^\d{4}$/.test(code)) {
                alert('Please enter a valid 4-digit postcode.');
                postcodeInput.focus();
                return false;
            }
            return true;
        }
        if (step === 2) {
            return true;
        }
        if (step === 3) {
            const s = getSelectedService();
            if (s === 'office') {
                const h = parseFloat(officeHoursInput.value);
                if (isNaN(h) || h <= 0) {
                    alert('Please enter valid office cleaning duration in hours.');
                    officeHoursInput.focus();
                    return false;
                }
            }
            return true;
        }
        return true;
    }

    function populateRecap() {
        if (!cachedBreakdown) return;
        document.getElementById('pce-recap-service').textContent = cachedBreakdown.serviceName;
        document.getElementById('pce-recap-loc').textContent = (cachedBreakdown.suburb ? cachedBreakdown.suburb + ', ' : '') + 'VIC ' + cachedBreakdown.postcode;
        document.getElementById('pce-recap-amount').textContent = '$' + cachedBreakdown.finalTotal.toFixed(2) + ' AUD';
    }

    // Attach click listeners to next & back buttons
    container.querySelectorAll('.pce-btn-next, .pce-btn-back').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetStep = parseInt(this.getAttribute('data-goto'), 10);
            if (targetStep) goToStep(targetStep);
        });
    });

    // Form Submission Handler
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        submitError.style.display = 'none';

        const name = (document.getElementById('pce_name').value || '').trim();
        const email = (document.getElementById('pce_email').value || '').trim();
        const phone = (document.getElementById('pce_phone').value || '').trim();
        const consent = document.getElementById('pce_consent').checked;
        const prefDate = (document.getElementById('pce_date').value || '').trim();
        const notes = (document.getElementById('pce_notes').value || '').trim();
        const hp = (document.getElementById('pce_hp_url').value || '').trim();

        if (!name) {
            showError('Please provide your full name.');
            document.getElementById('pce_name').focus();
            return;
        }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('Please provide a valid email address.');
            document.getElementById('pce_email').focus();
            return;
        }
        if (!phone) {
            showError('Please provide a contact phone number.');
            document.getElementById('pce_phone').focus();
            return;
        }
        if (!consent) {
            showError('Please confirm your consent to be contacted regarding this quote.');
            document.getElementById('pce_consent').focus();
            return;
        }

        // Date validation: not past date
        if (prefDate) {
            const todayStr = window.pceData.today_date || new Date().toISOString().split('T')[0];
            if (prefDate < todayStr) {
                showError('Preferred date cannot be in the past.');
                document.getElementById('pce_date').focus();
                return;
            }
        }

        // Prepare FormData
        const formData = new FormData();
        formData.append('action', 'pce_submit_quote');
        formData.append('nonce', nonce);
        formData.append('pce_hp_url', hp);
        formData.append('pce_render_ts', renderTs);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('suburb', suburbInput.value.trim());
        formData.append('postcode', postcodeInput.value.trim());
        formData.append('service', getSelectedService());
        formData.append('bedrooms', bedroomsSelect.value);
        formData.append('bathrooms', bathroomsSelect.value);
        formData.append('frequency', form.querySelector('input[name="frequency"]:checked') ? form.querySelector('input[name="frequency"]:checked').value : 'once');
        formData.append('office_hours', officeHoursInput.value);
        formData.append('preferred_date', prefDate);
        formData.append('notes', notes);
        formData.append('consent', consent ? '1' : '');

        // Loading state
        submitBtn.disabled = true;
        submitBtn.querySelector('.pce-btn-text').textContent = 'Submitting Quote...';
        submitBtn.querySelector('.pce-spinner').style.display = 'inline-block';

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.querySelector('.pce-btn-text').textContent = 'Submit Quote Enquiry';
            submitBtn.querySelector('.pce-spinner').style.display = 'none';

            if (data.success) {
                showSuccessScreen(data.data);
            } else {
                showError(data.data && data.data.message ? data.data.message : 'An error occurred. Please check your entries and retry.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.querySelector('.pce-btn-text').textContent = 'Submit Quote Enquiry';
            submitBtn.querySelector('.pce-spinner').style.display = 'none';
            showError('Network error while saving quote. Please check your connection or contact us directly.');
        });
    });

    function showError(msg) {
        submitError.textContent = msg;
        submitError.style.display = 'block';
        submitError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function showSuccessScreen(info) {
        stepPanels.forEach(p => p.style.display = 'none');
        container.querySelector('.pce-steps-nav').style.display = 'none';

        const successPanel = document.getElementById('pce-success-panel');
        document.getElementById('pce-success-name').textContent = info.customer_name;
        document.getElementById('pce-success-ref').textContent = '#' + info.enquiry_id;
        document.getElementById('pce-success-service').textContent = info.service_name;
        document.getElementById('pce-success-amount').textContent = info.total_amount;

        if (info.is_manual) {
            document.getElementById('pce-success-manual-note').style.display = 'block';
        } else {
            document.getElementById('pce-success-manual-note').style.display = 'none';
        }

        successPanel.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Restart button
    const restartBtn = document.getElementById('pce-btn-restart');
    if (restartBtn) {
        restartBtn.addEventListener('click', function () {
            form.reset();
            container.querySelector('.pce-steps-nav').style.display = 'flex';
            document.getElementById('pce-success-panel').style.display = 'none';
            syncServiceFields();
            goToStep(1);
        });
    }

    // Initialize initial state
    syncServiceFields();
    updateCoverageUI();
});
