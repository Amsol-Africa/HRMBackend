(function ($) {
    let availableItems = {
        allowances: [],
        deductions: [],
        reliefs: [],
        loans: [],
        advances: [],
    };

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    function formatNumber(number) {
        return Number(number).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function fetchAvailableItems(callback) {
        $.ajax({
            url: '/payroll/available-items',
            method: 'GET',
            success: function (response) {
                if (response.message === 'success') {
                    availableItems.allowances = response.data.allowances || [];
                    availableItems.deductions = response.data.deductions || [];
                    availableItems.reliefs = response.data.reliefs || [];
                    availableItems.loans = response.data.loans || [];
                    availableItems.advances = response.data.advances || [];
                    if (callback) callback();
                } else {
                    Swal.fire('Error!', 'Failed to fetch available items.', 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to fetch available items.', 'error');
            }
        });
    }

    function fetchDefaultAmount(type, itemId, callback) {
        $.ajax({
            url: `/payroll/default-amount/${type}/${itemId}`,
            method: 'GET',
            success: function (response) {
                if (response.message === 'success') {
                    callback(response.data.amount || 0);
                } else {
                    callback(0);
                }
            },
            error: function () {
                callback(0);
            }
        });
    }

    window.configurePayrollSettings = function () {
        const $form = $('#payrollForm');
        const formData = new FormData($form[0]);
        const exemptedEmployeesJson = formData.get('exempted_employees');
        const exemptedEmployees = exemptedEmployeesJson ? JSON.parse(exemptedEmployeesJson) : {};

        $('#allowancesTableBody, #deductionsTableBody, #reliefsTableBody, #absenteeismTableBody, #overtimeTableBody, #loansTableBody, #advancesTableBody')
            .html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');

        fetchAvailableItems(function () {
            $.ajax({
                url: '/payroll/fetch-employees-for-settings',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.message === 'success') {
                        const employees = response.data.employees || [];
                        const settingsId = response.data.settings_id || null;
                        $form.find('input[name="settings_id"]').remove();
                        $form.append(`<input type="hidden" name="settings_id" value="${settingsId || ''}">`);
                        const filteredEmployees = employees.filter(employee =>
                            exemptedEmployees[employee.id] === 0 || !(employee.id in exemptedEmployees)
                        );

                        const populateTable = (tableBodyId, type, generateSubscribed, generateAvailable) => {
                            const $tableBody = $(tableBodyId);
                            let html = '';
                            filteredEmployees.forEach(employee => {
                                html += `
                                    <tr data-employee-id="${employee.id}">
                                        <td>${employee.name}</td>
                                        <td>
                                            <div class="subscribed-items" id="subscribed-${type}-${employee.id}">
                                                ${generateSubscribed(employee)}
                                            </div>
                                        </td>
                                        ${type !== 'overtime' && type !== 'absenteeism' ? `
                                            <td>
                                                <div class="available-items" id="available-${type}-${employee.id}">
                                                    ${generateAvailable(employee)}
                                                </div>
                                            </td>
                                        ` : type === 'absenteeism' ? '' : '<td></td>'}
                                    </tr>
                                `;
                            });
                            $tableBody.html(html || `<tr><td colspan="${type === 'absenteeism' || type === 'overtime' ? 2 : 3}" class="text-center">No employees selected</td></tr>`);
                        };

                        populateTable('#allowancesTableBody', 'allowances', loadAllowanceInputs, loadAvailableAllowances);
                        populateTable('#deductionsTableBody', 'deductions', loadDeductionInputs, loadAvailableDeductions);
                        populateTable('#reliefsTableBody', 'reliefs', loadReliefInputs, loadAvailableReliefs);
                        populateTable('#loansTableBody', 'loans', loadLoanInputs, loadAvailableLoans);
                        populateTable('#advancesTableBody', 'advances', loadAdvanceInputs, loadAvailableAdvances);
                        populateTable('#absenteeismTableBody', 'absenteeism', employee => `
                            <input type="number" class="form-control form-control-sm absenteeism-charge" name="absenteeism_charge[${employee.id}]" value="${employee.absenteeism_charge.amount || 0}" min="0" step="0.01">
                        `, () => '');
                        populateTable('#overtimeTableBody', 'overtime', loadOvertimeInputs, () => '');

                        attachEventListeners();
                    } else {
                        Swal.fire('Error!', response.message || 'Failed to fetch employees.', 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to load employees for settings.', 'error');
                }
            });
        });
    };

    function loadAllowanceInputs(employee) {
        const allowances = Object.values(employee.allowances || {}).reduce((unique, allowance) => {
            if (!unique[allowance.item_id] && allowance.is_active !== false) { // Only active items
                unique[allowance.item_id] = allowance;
            }
            return unique;
        }, {});
        let html = '';
        if (Object.keys(allowances).length === 0) {
            return '<p>No subscribed allowances.</p>';
        }
        Object.values(allowances).forEach(allowance => {
            const amount = parseFloat(allowance.amount) || 0;
            const rate = parseFloat(allowance.rate) || 0;
            const displayName = rate > 0
                ? `${allowance.item_name} (${rate.toFixed(2)}%)`
                : `${allowance.item_name} (${formatNumber(amount)})`;
            html += `
            <div class="form-group mb-2" id="allowances-subscribed-${employee.id}-${allowance.item_id}">
                <div class="form-check">
                    <input class="form-check-input allowance-checkbox" type="checkbox" name="allowances[${employee.id}][${allowance.item_id}]" value="1" checked data-allowance-id="${allowance.item_id}">
                    <label class="form-check-label">${displayName}</label>
                </div>
                ${rate > 0 ? `
                    <input type="number" class="form-control form-control-sm allowance-rate mt-1" name="allowance_rates[${employee.id}][${allowance.item_id}]" value="${rate}" min="0" step="0.01" placeholder="Rate">
                ` : `
                    <input type="number" class="form-control form-control-sm allowance-amount amount-input mt-1" name="allowance_amounts[${employee.id}][${allowance.item_id}]" value="${amount}" min="0" step="0.01" placeholder="Amount">
                `}
            </div>
        `;
        });
        return html;
    }

    function loadAvailableAllowances(employee) {
        const subscribedIds = Object.values(employee.allowances || {})
            .filter(a => a.is_active !== false)
            .map(a => a.item_id);
        const inactiveSubscribed = Object.values(employee.allowances || {})
            .filter(a => a.is_active === false)
            .map(a => ({
                id: a.item_id,
                name: a.item_name,
                amount: a.amount || 0,
                rate: a.rate || 0,
                type: a.rate > 0 ? 'rate' : 'fixed',
                calculation_basis: availableItems.allowances.find(i => i.id === a.item_id)?.calculation_basis || ''
            }));
        const available = [
            ...availableItems.allowances.filter(item => !subscribedIds.includes(item.id)),
            ...inactiveSubscribed
        ].reduce((unique, item) => {
            if (!unique[item.id]) unique[item.id] = item;
            return unique;
        }, {});
        let html = '';
        Object.values(available).forEach(item => {
            const displayValue = item.type === 'rate'
                ? `${parseFloat(item.rate || 0).toFixed(2)}% of ${item.calculation_basis || 'N/A'}`
                : formatNumber(item.amount || 0);
            html += `
            <div class="form-check mb-2" id="allowances-available-${employee.id}-${item.id}">
                <input class="form-check-input allowance-checkbox" type="checkbox" name="allowances[${employee.id}][${item.id}]" value="1" data-allowance-id="${item.id}">
                <label class="form-check-label">${item.name} (${displayValue})</label>
            </div>
        `;
        });
        return html || '<p>No available allowances.</p>';
    }

    function loadDeductionInputs(employee) {
        const deductions = Object.values(employee.deductions || {}).reduce((unique, deduction) => {
            if (!unique[deduction.item_id] && deduction.is_active !== false) {
                unique[deduction.item_id] = deduction;
            }
            return unique;
        }, {});
        let html = '';
        if (Object.keys(deductions).length === 0) {
            return '<p>No subscribed deductions.</p>';
        }
        Object.values(deductions).forEach(deduction => {
            const amount = parseFloat(deduction.amount) || 0;
            const rate = parseFloat(deduction.rate) || 0;
            const displayName = rate > 0
                ? `${deduction.item_name} (${rate.toFixed(2)}%)`
                : `${deduction.item_name} (${formatNumber(amount)})`;
            html += `
            <div class="form-group mb-2" id="deductions-subscribed-${employee.id}-${deduction.item_id}">
                <div class="form-check">
                    <input class="form-check-input deduction-checkbox" type="checkbox" name="deductions[${employee.id}][${deduction.item_id}]" value="1" checked data-deduction-id="${deduction.item_id}">
                    <label class="form-check-label">${displayName}</label>
                </div>
                ${rate > 0 ? `
                    <input type="number" class="form-control form-control-sm deduction-rate mt-1" name="deduction_rates[${employee.id}][${deduction.item_id}]" value="${rate}" min="0" step="0.01" placeholder="Rate">
                ` : `
                    <input type="number" class="form-control form-control-sm deduction-amount amount-input mt-1" name="deduction_amounts[${employee.id}][${deduction.item_id}]" value="${amount}" min="0" step="0.01" placeholder="Amount">
                `}
            </div>
        `;
        });
        return html;
    }

    function loadAvailableDeductions(employee) {
        const subscribedIds = Object.values(employee.deductions || {})
            .filter(d => d.is_active !== false)
            .map(d => d.item_id);
        const inactiveSubscribed = Object.values(employee.deductions || {})
            .filter(d => d.is_active === false)
            .map(d => ({
                id: d.item_id,
                name: d.item_name,
                amount: d.amount || 0,
                rate: d.rate || 0,
                type: d.rate > 0 ? 'rate' : 'fixed',
                calculation_basis: availableItems.deductions.find(i => i.id === d.item_id)?.calculation_basis || ''
            }));
        const available = [
            ...availableItems.deductions.filter(item => !subscribedIds.includes(item.id)),
            ...inactiveSubscribed
        ].reduce((unique, item) => {
            if (!unique[item.id]) unique[item.id] = item;
            return unique;
        }, {});
        let html = '';
        Object.values(available).forEach(item => {
            const displayValue = item.type === 'rate'
                ? `${parseFloat(item.rate || 0).toFixed(2)}% of ${item.calculation_basis || 'N/A'}`
                : formatNumber(item.amount || 0);
            html += `
            <div class="form-check mb-2" id="deductions-available-${employee.id}-${item.id}">
                <input class="form-check-input deduction-checkbox" type="checkbox" name="deductions[${employee.id}][${item.id}]" value="1" data-deduction-id="${item.id}">
                <label class="form-check-label">${item.name} (${displayValue})</label>
            </div>
        `;
        });
        return html || '<p>No available deductions.</p>';
    }

    function loadReliefInputs(employee) {
        const reliefs = Object.values(employee.reliefs || {}).reduce((unique, relief) => {
            if (!unique[relief.item_id] && relief.is_active !== false) {
                unique[relief.item_id] = relief;
            }
            return unique;
        }, {});
        let html = '';
        if (Object.keys(reliefs).length === 0) {
            return '<p>No subscribed reliefs.</p>';
        }
        Object.values(reliefs).forEach(relief => {
            const amount = parseFloat(relief.amount) || 0;
            const displayName = `${relief.item_name} (${formatNumber(amount)})`;
            const itemId = relief.item_id || '';
            html += `
            <div class="form-group mb-2" id="reliefs-subscribed-${employee.id}-${itemId}">
                <div class="form-check">
                    <input class="form-check-input relief-checkbox" type="checkbox" name="reliefs[${employee.id}][${itemId}]" value="1" checked data-relief-id="${itemId}">
                    <label class="form-check-label">${displayName}</label>
                </div>
                <input type="number" class="form-control form-control-sm relief-amount amount-input mt-1" name="relief_amounts[${employee.id}][${itemId}]" value="${amount}" min="0" step="0.01" placeholder="Amount">
            </div>
        `;
        });
        return html;
    }

    function loadAvailableReliefs(employee) {
        const subscribedIds = Object.values(employee.reliefs || {})
            .filter(r => r.is_active !== false)
            .map(r => r.item_id)
            .filter(id => id);
        const inactiveSubscribed = Object.values(employee.reliefs || {})
            .filter(r => r.is_active === false)
            .map(r => ({
                id: r.item_id,
                name: r.item_name,
                amount: r.amount || 0
            }));
        const available = [
            ...availableItems.reliefs.filter(item => !subscribedIds.includes(item.id)),
            ...inactiveSubscribed
        ].reduce((unique, item) => {
            if (!unique[item.id]) unique[item.id] = item;
            return unique;
        }, {});
        let html = '';
        Object.values(available).forEach(item => {
            const displayValue = formatNumber(item.amount || 0);
            html += `
            <div class="form-check mb-2" id="reliefs-available-${employee.id}-${item.id}">
                <input class="form-check-input relief-checkbox" type="checkbox" name="reliefs[${employee.id}][${item.id}]" value="1" data-relief-id="${item.id}">
                <label class="form-check-label">${item.name} (${displayValue})</label>
            </div>
        `;
        });
        return html || '<p>No available reliefs.</p>';
    }

    function loadOvertimeInputs(employee) {
        const overtimes = Object.values(employee.overtimes || {});
        let html = '';
        if (overtimes.length === 0) {
            return '<p>No overtime available.</p>';
        }
        overtimes.forEach(overtime => {
            const hours = parseFloat(overtime.amount) || 0;
            const isSelected = overtime.is_active !== false;
            const formattedDate = formatDate(overtime.item_name.split('on ')[1] || new Date());
            const displayName = `Overtime on ${formattedDate} (${formatNumber(hours)})`;
            html += `
            <div class="form-group mb-2" id="overtime-${employee.id}-${overtime.item_id}">
                <div class="form-check">
                    <input class="form-check-input overtime-checkbox" type="checkbox" 
                           name="overtime[${employee.id}][${overtime.item_id}]" value="1" 
                           ${isSelected ? 'checked' : ''} data-overtime-id="${overtime.item_id}">
                    <label class="form-check-label">${displayName}</label>
                </div>
                <div class="mt-1">
                    <input type="number" class="form-control form-control-sm overtime-hours" 
                           name="overtime_hours[${employee.id}][${overtime.item_id}]" value="${hours}" 
                           min="0" step="0.1" placeholder="Hours Worked">
                </div>
            </div>
        `;
        });
        return html;
    }

    function loadLoanInputs(employee) {
        const loans = Object.values(employee.loans || {}).reduce((unique, loan) => {
            if (!unique[loan.item_id] && loan.is_active !== false) {
                unique[loan.item_id] = loan;
            }
            return unique;
        }, {});
        let html = '';
        if (Object.keys(loans).length === 0) {
            return '<p>No subscribed loans.</p>';
        }
        Object.values(loans).forEach(loan => {
            const remaining = parseFloat(loan.amount) || 0;
            const amountToRecover = parseFloat(loan.amount) || remaining;
            const formattedDate = formatDate(loan.item_name.split('started ')[1] || new Date());
            if (remaining > 0) {
                const displayName = `Loan on ${formattedDate} (Remaining: ${formatNumber(remaining)})`;
                html += `
                <div class="form-group mb-2" id="loans-subscribed-${employee.id}-${loan.item_id}">
                    <div class="form-check">
                        <input class="form-check-input loan-checkbox" type="checkbox" name="loans[${employee.id}][${loan.item_id}]" value="1" checked data-loan-id="${loan.item_id}" data-max-amount="${remaining}">
                        <label class="form-check-label">${displayName}</label>
                    </div>
                    <input type="number" class="form-control form-control-sm loan-amount amount-input mt-1" name="loan_amounts[${employee.id}][${loan.item_id}]" value="${amountToRecover}" min="0" max="${remaining}" step="0.01" placeholder="Amount to Recover">
                </div>
            `;
            }
        });
        return html;
    }

    function loadAvailableLoans(employee) {
        const subscribedIds = Object.values(employee.loans || {})
            .filter(l => l.is_active !== false)
            .map(l => l.item_id);
        const inactiveSubscribed = Object.values(employee.loans || {})
            .filter(l => l.is_active === false)
            .map(l => ({
                id: l.item_id,
                employee_id: employee.id,
                start_date: l.item_name.split('started ')[1] || new Date(),
                amount: l.amount || 0,
                remaining: l.amount || 0
            }));
        const available = [
            ...availableItems.loans.filter(item => item.employee_id === employee.id && !subscribedIds.includes(item.id)),
            ...inactiveSubscribed
        ].reduce((unique, item) => {
            if (!unique[item.id]) unique[item.id] = item;
            return unique;
        }, {});
        let html = '';
        Object.values(available).forEach(item => {
            const remaining = parseFloat(item.remaining) || 0;
            const formattedDate = formatDate(item.start_date);
            const displayName = `Loan on ${formattedDate} (Remaining: ${formatNumber(remaining)})`;
            html += `
            <div class="form-check mb-2" id="loans-available-${employee.id}-${item.id}">
                <input class="form-check-input loan-checkbox" type="checkbox" name="loans[${employee.id}][${item.id}]" value="1" data-loan-id="${item.id}" data-max-amount="${remaining}">
                <label class="form-check-label">${displayName}</label>
            </div>
        `;
        });
        return html || '<p>No available loans.</p>';
    }

    function loadAdvanceInputs(employee) {
        const advances = Object.values(employee.advances || {}).reduce((unique, advance) => {
            if (!unique[advance.item_id] && advance.is_active !== false) {
                unique[advance.item_id] = advance;
            }
            return unique;
        }, {});
        let html = '';
        if (Object.keys(advances).length === 0) {
            return '<p>No subscribed advances.</p>';
        }
        Object.values(advances).forEach(advance => {
            const amount = parseFloat(advance.amount) || 0;
            const amountToRecover = parseFloat(advance.amount) || amount;
            const formattedDate = formatDate(advance.item_name.split('on ')[1] || new Date());
            const displayName = `Advance on ${formattedDate} (Amount: ${formatNumber(amount)})`;
            html += `
            <div class="form-group mb-2" id="advances-subscribed-${employee.id}-${advance.item_id}">
                <div class="form-check">
                    <input class="form-check-input advance-checkbox" type="checkbox" name="advances[${employee.id}][${advance.item_id}]" value="1" checked data-advance-id="${advance.item_id}" data-max-amount="${amount}">
                    <label class="form-check-label">${displayName}</label>
                </div>
                <input type="number" class="form-control form-control-sm advance-amount amount-input mt-1" name="advance_amounts[${employee.id}][${advance.item_id}]" value="${amountToRecover}" min="0" max="${amount}" step="0.01" placeholder="Amount to Recover">
            </div>
        `;
        });
        return html;
    }

    function loadAvailableAdvances(employee) {
        const subscribedIds = Object.values(employee.advances || {})
            .filter(a => a.is_active !== false)
            .map(a => a.item_id);
        const inactiveSubscribed = Object.values(employee.advances || {})
            .filter(a => a.is_active === false)
            .map(a => ({
                id: a.item_id,
                employee_id: employee.id,
                date: a.item_name.split('on ')[1] || new Date(),
                amount: a.amount || 0
            }));
        const available = [
            ...availableItems.advances.filter(item => item.employee_id === employee.id && !subscribedIds.includes(item.id)),
            ...inactiveSubscribed
        ].reduce((unique, item) => {
            if (!unique[item.id]) unique[item.id] = item;
            return unique;
        }, {});
        let html = '';
        Object.values(available).forEach(item => {
            const amount = parseFloat(item.amount) || 0;
            const formattedDate = formatDate(item.date);
            const displayName = `Advance on ${formattedDate} (Amount: ${formatNumber(amount)})`;
            html += `
            <div class="form-check mb-2" id="advances-available-${employee.id}-${item.id}">
                <input class="form-check-input advance-checkbox" type="checkbox" name="advances[${employee.id}][${item.id}]" value="1" data-advance-id="${item.id}" data-max-amount="${amount}">
                <label class="form-check-label">${displayName}</label>
            </div>
        `;
        });
        return html || '<p>No available advances.</p>';
    }

    function handleItemToggle(checkbox, type, employeeId, itemId, itemName, additionalInfo = '') {
        const $checkbox = $(checkbox);
        const $subscribedContainer = $(`#subscribed-${type}-${employeeId}`);
        const $availableContainer = $(`#available-${type}-${employeeId}`);
        const isChecked = $checkbox.is(':checked');
        const maxAmount = $checkbox.data('max-amount') || null;
        const item = availableItems[type].find(i => i.id === itemId) || {};
        const defaultAmount = parseFloat(item.amount || 0);
        const defaultRate = parseFloat(item.rate || 0);
        const calculationBasis = item.calculation_basis || '';

        // Remove existing entries
        $(`#${type}-subscribed-${employeeId}-${itemId}`).remove();
        $(`#${type}-available-${employeeId}-${itemId}`).remove();

        if (isChecked) {
            // Check if item already exists in subscribed section (prevent duplicates)
            if ($subscribedContainer.find(`#${type}-subscribed-${employeeId}-${itemId}`).length === 0) {
                const isRateBased = item.type === 'rate';
                const subscribedHtml = `
                    <div class="form-group mb-2" id="${type}-subscribed-${employeeId}-${itemId}">
                        <div class="form-check">
                            <input class="form-check-input ${type}-checkbox" type="checkbox" 
                                   name="${type}[${employeeId}][${itemId}]" value="1" checked 
                                   data-${type}-id="${itemId}" ${maxAmount ? `data-max-amount="${maxAmount}"` : ''}>
                            <label class="form-check-label">${itemName}${calculationBasis ? ` (${calculationBasis})` : ''}${additionalInfo}</label>
                        </div>
                        ${isRateBased ? `
                            <input type="number" class="form-control form-control-sm ${type}-rate mt-1" 
                                   name="${type}_rates[${employeeId}][${itemId}]" value="${defaultRate}" 
                                   min="0" step="0.01" placeholder="Rate">
                        ` : `
                            <input type="number" class="form-control form-control-sm ${type}-amount amount-input mt-1" 
                                   name="${type}_amounts[${employeeId}][${itemId}]" value="${defaultAmount}" 
                                   min="0" ${maxAmount ? `max="${maxAmount}"` : ''} step="0.01" placeholder="Amount">
                        `}
                    </div>
                `;
                $subscribedContainer.append(subscribedHtml);
                if ($subscribedContainer.find('p').length > 0) $subscribedContainer.find('p').remove();
            }
        } else {
            // Add to available only if not already present
            if ($availableContainer.find(`#${type}-available-${employeeId}-${itemId}`).length === 0) {
                const availableHtml = `
                    <div class="form-check mb-2" id="${type}-available-${employeeId}-${itemId}">
                        <input class="form-check-input ${type}-checkbox" type="checkbox" 
                               name="${type}[${employeeId}][${itemId}]" value="1" 
                               data-${type}-id="${itemId}" ${maxAmount ? `data-max-amount="${maxAmount}"` : ''}>
                        <label class="form-check-label">${itemName}${calculationBasis ? ` (${calculationBasis})` : ''}${additionalInfo}</label>
                    </div>
                `;
                $availableContainer.append(availableHtml);
                if ($availableContainer.find('p').length > 0 && $availableContainer.find('.form-check').length > 0) {
                    $availableContainer.find('p').remove();
                }
            }
            if ($subscribedContainer.find('.form-group').length === 0) {
                $subscribedContainer.html(`<p>No subscribed ${type}.</p>`);
            }
        }
        attachEventListenerForType(type, employeeId, itemId);
    }

    function attachEventListeners() {
        $('.allowance-checkbox, .allowances-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('allowance-id') || $(this).data('allowances-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'allowances', employeeId, itemId, itemName, additionalInfo);
        });

        $('.deduction-checkbox, .deductions-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('deduction-id') || $(this).data('deductions-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'deductions', employeeId, itemId, itemName, additionalInfo);
        });

        $('.relief-checkbox, .reliefs-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('relief-id') || $(this).data('reliefs-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'reliefs', employeeId, itemId, itemName, additionalInfo);
        });

        $('.loan-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('loan-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'loans', employeeId, itemId, itemName, additionalInfo);
        });

        $('.advance-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('advance-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'advances', employeeId, itemId, itemName, additionalInfo);
        });

        $('.overtime-checkbox').off('change').on('change', function () {
            const employeeId = $(this).closest('tr').data('employee-id');
            const itemId = $(this).data('overtime-id');
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, 'overtime', employeeId, itemId, itemName, additionalInfo);
        });
    }

    function attachEventListenerForType(type, employeeId, itemId) {
        const $checkbox = $(`#${type}-subscribed-${employeeId}-${itemId} .${type}-checkbox, #${type}-available-${employeeId}-${itemId} .${type}-checkbox`);
        $checkbox.off('change').on('change', function () {
            const itemName = $(this).next('label').text().replace(/ \(.+\)/g, '');
            const additionalInfo = $(this).next('label').text().match(/ \(.+\)/g) || '';
            handleItemToggle(this, type, employeeId, itemId, itemName, additionalInfo);
        });
    }

    window.savePayrollSettings = function () {
        const formData = new FormData(document.getElementById("payrollForm"));
        const year = formData.get('year');
        const month = formData.get('month');
        const employees = {};
        let allZeroAmountItems = [];

        const collectAllItems = (type, tableBody, checkboxClasses, amountClasses, rateClass, idAttrs) => {
            const itemsByEmployee = {};
            const zeroAmountItems = [];
            $(tableBody).find('tr[data-employee-id]').each(function () {
                const employeeId = $(this).data('employee-id');
                if (!itemsByEmployee[employeeId]) itemsByEmployee[employeeId] = {};

                // Collect both subscribed and available items
                const $checkboxes = $(this).find(`#subscribed-${type}-${employeeId} .${checkboxClasses.join(', .')}, #available-${type}-${employeeId} .${checkboxClasses.join(', .')}`);
                $checkboxes.each(function () {
                    const itemId = $(this).data(idAttrs[0]) || $(this).data(idAttrs[1]);
                    if (!itemId) return;

                    const isActive = $(this).is(':checked');
                    const $group = $(this).closest('.form-group');
                    const $amountInput = $group.find(`.${amountClasses.join(', .')}`);
                    const $rateInput = rateClass ? $group.find(`.${rateClass}`) : null;
                    const amount = $amountInput.length ? parseFloat($amountInput.val() || 0) : 0;
                    const rate = $rateInput ? parseFloat($rateInput.val() || 0) : 0;
                    const itemName = $(this).next('label').text().trim();

                    itemsByEmployee[employeeId][itemId] = { is_active: isActive, amount: amount, rate: rate };
                    if (isActive && amount === 0 && (!rateClass || rate === 0)) {
                        zeroAmountItems.push(`${itemName} for Employee ID: ${employeeId}`);
                    }
                });
            });
            return { itemsByEmployee, zeroAmountItems };
        };

        const tables = [
            { type: 'allowances', tableBody: '#allowancesTableBody', checkboxClasses: ['allowance-checkbox', 'allowances-checkbox'], amountClasses: ['allowance-amount', 'allowances-amount'], rateClass: 'allowance-rate', idAttrs: ['allowance-id', 'allowances-id'] },
            { type: 'deductions', tableBody: '#deductionsTableBody', checkboxClasses: ['deduction-checkbox', 'deductions-checkbox'], amountClasses: ['deduction-amount', 'deductions-amount'], rateClass: 'deduction-rate', idAttrs: ['deduction-id', 'deductions-id'] },
            { type: 'reliefs', tableBody: '#reliefsTableBody', checkboxClasses: ['relief-checkbox', 'reliefs-checkbox'], amountClasses: ['relief-amount', 'reliefs-amount'], rateClass: null, idAttrs: ['relief-id', 'reliefs-id'] },
            { type: 'loans', tableBody: '#loansTableBody', checkboxClasses: ['loan-checkbox'], amountClasses: ['loan-amount'], rateClass: null, idAttrs: ['loan-id'] },
            { type: 'advances', tableBody: '#advancesTableBody', checkboxClasses: ['advance-checkbox'], amountClasses: ['advance-amount'], rateClass: null, idAttrs: ['advance-id'] },
            { type: 'absenteeism', tableBody: '#absenteeismTableBody' },
            { type: 'overtime', tableBody: '#overtimeTableBody', checkboxClasses: ['overtime-checkbox'], amountClasses: ['overtime-hours'], rateClass: null, idAttrs: ['overtime-id'] },
        ];

        tables.forEach(({ type, tableBody, checkboxClasses, amountClasses, rateClass, idAttrs }) => {
            $(tableBody).find('tr[data-employee-id]').each(function () {
                const employeeId = $(this).data('employee-id');
                if (!employees[employeeId]) {
                    employees[employeeId] = {
                        employee_id: employeeId,
                        allowances: {}, deductions: {}, reliefs: {},
                        absenteeism_charge: 0, overtime: {}, loans: {}, advances: {}
                    };
                }

                if (type === 'absenteeism') {
                    const charge = parseFloat($(this).find(`input[name="absenteeism_charge[${employeeId}]"]`).val() || 0);
                    employees[employeeId].absenteeism_charge = charge;
                } else {
                    const { itemsByEmployee, zeroAmountItems } = collectAllItems(type, tableBody, checkboxClasses || [], amountClasses || [], rateClass, idAttrs || []);
                    Object.assign(employees[employeeId][type], itemsByEmployee[employeeId] || {});
                    allZeroAmountItems = allZeroAmountItems.concat(zeroAmountItems);
                }
            });
        });

        const employeesArray = Object.values(employees).map(employee => ({
            id: employee.employee_id,
            allowances: employee.allowances,
            deductions: employee.deductions,
            reliefs: employee.reliefs,
            overtime: employee.overtime,
            loans: employee.loans,
            advances: employee.advances,
            absenteeism_charge: { amount: employee.absenteeism_charge }
        }));

        if (allZeroAmountItems.length > 0) {
            Swal.fire({
                title: 'Zero Amount Detected',
                html: `The following items have zero amounts/rates:<ul>${allZeroAmountItems.map(item => `<li>${item}</li>`).join('')}</ul>Proceed?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Edit'
            }).then((result) => {
                if (result.isConfirmed) saveData(year, month, employeesArray);
            });
        } else {
            saveData(year, month, employeesArray);
        }
    };

    function saveData(year, month, employeesArray) {
        const data = {
            year: year,
            month: month,
            employees: employeesArray,
            _token: $('meta[name="csrf-token"]').attr('content'),
        };

        console.log('Data being sent to backend:', JSON.stringify(data, null, 2));

        $.ajax({
            url: '/payroll/save-settings',
            method: 'POST',
            data: data,
            success: function (response) {
                if (response.message === 'success') {
                    Swal.fire('Success!', 'Payroll settings saved successfully.', 'success');
                    togglePayrollSettings();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to save settings.', 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to save payroll settings.', 'error');
            }
        });
    }

    function collectAllSettings() {
        const employees = {};
        const tables = [
            { type: 'allowances', tableBody: '#allowancesTableBody', checkboxClasses: ['allowance-checkbox'], amountClasses: ['allowance-amount'], rateClass: 'allowance-rate', idAttr: 'allowance-id' },
            { type: 'deductions', tableBody: '#deductionsTableBody', checkboxClasses: ['deduction-checkbox'], amountClasses: ['deduction-amount'], rateClass: 'deduction-rate', idAttr: 'deduction-id' },
            { type: 'reliefs', tableBody: '#reliefsTableBody', checkboxClasses: ['relief-checkbox'], amountClasses: ['relief-amount'], rateClass: null, idAttr: 'relief-id' },
            { type: 'loans', tableBody: '#loansTableBody', checkboxClasses: ['loan-checkbox'], amountClasses: ['loan-amount'], rateClass: null, idAttr: 'loan-id' },
            { type: 'advances', tableBody: '#advancesTableBody', checkboxClasses: ['advance-checkbox'], amountClasses: ['advance-amount'], rateClass: null, idAttr: 'advance-id' },
            { type: 'absenteeism', tableBody: '#absenteeismTableBody' },
            { type: 'overtime', tableBody: '#overtimeTableBody', checkboxClasses: ['overtime-checkbox'], amountClasses: ['overtime-hours'], rateClass: null, idAttr: 'overtime-id' },
        ];

        tables.forEach(({ type, tableBody, checkboxClasses, amountClasses, rateClass, idAttr }) => {
            $(tableBody).find('tr[data-employee-id]').each(function () {
                const employeeId = $(this).data('employee-id');
                if (!employees[employeeId]) {
                    employees[employeeId] = {
                        employee_id: employeeId,
                        allowances: {}, deductions: {}, reliefs: {},
                        absenteeism_charge: 0, overtime: {}, loans: {}, advances: {}
                    };
                }

                if (type === 'absenteeism') {
                    employees[employeeId].absenteeism_charge = parseFloat($(this).find(`input[name="absenteeism_charge[${employeeId}]"]`).val() || 0);
                } else {
                    $(this).find(`#subscribed-${type}-${employeeId} .${checkboxClasses.join(', .')}`).each(function () {
                        const itemId = $(this).data(idAttr);
                        const isActive = $(this).is(':checked');
                        if (!itemId || !isActive) return;

                        const $group = $(this).closest('.form-group');
                        const $amountInput = $group.find(`.${amountClasses.join(', .')}`);
                        const $rateInput = rateClass ? $group.find(`.${rateClass}`) : null;
                        const amount = $amountInput.length ? parseFloat($amountInput.val() || 0) : 0;
                        const rate = $rateInput ? parseFloat($rateInput.val() || 0) : 0;

                        employees[employeeId][type][itemId] = { is_active: true, amount, rate };
                    });
                }
            });
        });

        return Object.values(employees);
    }

    window.processPayroll = function () {
        const formData = new FormData(document.getElementById("payrollForm"));
        const $previewContainer = $("#payrollPreviewContainer");
        const $previewLoader = $("#previewLoader");
        const settingsData = collectAllSettings();
        formData.append('settings', JSON.stringify(settingsData));

        // Keep exempted_employees JSON string for backend filtering
        console.log('Form Data:', JSON.stringify(Object.fromEntries(formData.entries()), null, 2));

        $previewContainer.empty();
        $previewLoader.show();

        $.ajax({
            url: '/payroll/preview',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $previewLoader.hide();

                if (response.message === "success") {
                    $previewContainer.html(response.data.html);
                    $('#payrollForm').hide();
                    $('#previewTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        searching: true,
                        ordering: true,
                        paging: true,
                        language: { search: "Filter:" }
                    });

                    $previewContainer.append(`
                        <div class="mt-4">
                            <button type="button" class="btn btn-success px-5 py-2 rounded-3 shadow-sm me-2" onclick="submitPayroll()">
                                <i class="fa fa-save me-2"></i> Submit Payroll
                            </button>
                            <button type="button" class="btn btn-secondary px-5 py-2 rounded-3 shadow-sm" onclick="$('#payrollPreviewContainer').empty(); $('#payrollForm').show();">
                                <i class="fa fa-times me-2"></i> Cancel
                            </button>
                        </div>
                    `);
                } else {
                    Swal.fire('Error!', response.error || 'Failed to preview payroll.', 'error');
                }
            },
            error: function (xhr) {
                $previewLoader.hide();
                console.log("Error Response:", xhr.responseJSON);

                if (xhr.status === 400 && xhr.responseJSON?.type === "warnings") {
                    let warningMessage = '<p>Please resolve the following issues before proceeding:</p><ul>';
                    for (const [employeeId, warningData] of Object.entries(xhr.responseJSON.warnings)) {
                        const { name, employee_code, messages } = warningData;
                        warningMessage += `<li><strong>${name} (${employee_code}):</strong> ${messages.join(', ')}</li>`;
                    }
                    warningMessage += '</ul><p>Correct these issues and try again.</p>';

                    Swal.fire({
                        title: 'Warning!',
                        html: warningMessage,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire('Error!', xhr.responseJSON?.error || 'Failed to preview payroll.', 'error');
            }
        });
    };

    window.submitPayroll = function () {
        const $previewLoader = $("#previewLoader");
        $previewLoader.show();

        $.ajax({
            url: '/payroll/store',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                $previewLoader.hide();
                if (response.message === 'success') {
                    Swal.fire('Success!', 'Payroll processed and stored successfully.', 'success').then(() => {
                        window.location.href = response.data.redirect_url || '/payroll';
                    });
                } else {
                    Swal.fire('Error!', response.error || 'Failed to store payroll.', 'error');
                }
            },
            error: function (xhr) {
                $previewLoader.hide();
                Swal.fire('Error!', xhr.responseJSON?.error || 'Failed to store payroll.', 'error');
            }
        });
    };
})(jQuery);