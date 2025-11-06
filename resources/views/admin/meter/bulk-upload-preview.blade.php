@extends('layouts.main')
@section('content')

<div class="content">
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Bulk Meter Upload - Preview & Validate</h4>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif

        <!-- Upload Section -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Step 1: Upload CSV File</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select CSV File (Max 100 rows)</label>
                            <input type="file" id="csvFile" class="form-control" accept=".csv" />
                            <small class="form-text text-muted">
                                <strong class="text-danger">⚠️ Only CSV files (.csv) are accepted.</strong> Excel files (.xlsx, .xls) and other formats will be rejected.<br>
                                <strong>Required columns:</strong> meterno, metermodel, accountno, transformer_id, isdualtariff, oldsgc, newsgc, newtariffid, oldtariffid, newtariffdual, oldtariffdual, newsgcdual, oldsgcdual, krn1, krn2, needkct, credittype
                                <br><strong>Note:</strong> Dual tariff settings (isdualtariff, newtariffdual, oldtariffdual, newsgcdual, oldsgcdual) must be configured in the spreadsheet and cannot be modified during preview.
                                <a href="/asset/meter_upload_sample.csv" download class="btn btn-sm btn-outline-primary ms-2"><i class="mdi mdi-download"></i> Download Sample CSV</a>
                            </small>                        </div>

                        @if(Auth::user()->role == 0)
                        <div class="mb-3">
                            <label class="form-label">Select Estate</label>
                            <select id="estateSelect" class="form-control" required>
                                <option value="">Choose Estate</option>
                                @foreach($estates as $estate)
                                    <option value="{{$estate->id}}">{{$estate->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Status -->
        <div class="row" id="processingStatus" style="display: none;">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Processing your CSV file...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="row" id="previewSection" style="display: none;">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Step 2: Review & Validate Data</h5>
                            <div>
                                <span id="validationSummary" class="badge bg-secondary">Ready for validation</span>
                                <button id="validateBtn" class="btn btn-warning ms-2">Validate All</button>
                                <button id="saveBtn" class="btn btn-success ms-2" style="display: none;">Save to Database</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="validationResults" class="mb-3"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="previewTable">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Status</th>
                                        <th>Meter No</th>
                                        <th>Model</th>
                                        <th>Account No</th>
                                        <th>Transformer</th>
                                        <th>Dual Tariff</th>
                                        <th>Old SGC</th>
                                        <th>New SGC</th>
                                        <th>NEPA Tariffs<br><small class="text-muted">Top: New     Bottom: Old</small></th>
                                        <th>Generator Tariffs<br><small class="text-muted">Top: New     Bottom: Old</small></th>
                                        <th>Generator SGC<br><small class="text-muted">Top: New     Bottom: Old</small></th>
                                        <th>KRN Keys<br><small class="text-muted">Top: KRN1     Bottom: KRN2</small></th>
                                        <th>Need KCT</th>
                                        <th>Credit Type</th>
                                        <th>Errors</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// CSV-only bulk meter upload script
let csvData = [];
let validatedData = [];
let existingMeterNumbers = [];

document.getElementById('csvFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const fileName = file.name.toLowerCase();
    const fileExtension = fileName.split('.').pop();

    // Validate file type - only CSV allowed
    if (fileExtension !== 'csv') {
        alert('❌ Invalid File Type\n\nOnly CSV files are allowed.\n\nYou uploaded: .' + fileExtension + '\nRequired: .csv\n\nPlease select a valid CSV file.');
        e.target.value = ''; // Clear the file input
        return;
    }

    // Additional MIME type check
    const validMimeTypes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'];
    if (file.type && !validMimeTypes.includes(file.type)) {
        alert('❌ Invalid File Type\n\nThe selected file does not appear to be a valid CSV file.\n\nMIME Type: ' + file.type + '\n\nPlease ensure you are uploading a proper CSV file.');
        e.target.value = ''; // Clear the file input
        return;
    }

    // Show processing status
    document.getElementById('processingStatus').style.display = 'block';
    document.getElementById('previewSection').style.display = 'none';

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            // Parse CSV only
            const data = parseCSV(e.target.result);

            // Limit to 100 rows
            if (data.length > 100) {
                data = data.slice(0, 100);
                alert('⚠️ File Size Limit\n\nYour file contains more than 100 rows.\nOnly the first 100 rows will be processed for optimal performance.');
            }

            // Normalize metermodel and credittype to lowercase for consistency
            data = data.map(row => {
                if (row.metermodel) {
                    row.metermodel = row.metermodel.toLowerCase();
                }
                if (row.credittype) {
                    row.credittype = row.credittype.toLowerCase();
                }
                return row;
            });

            csvData = data;
            displayPreview();

        } catch (error) {
            alert('❌ Error Reading File\n\n' + error.message + '\n\nPlease ensure your CSV file is properly formatted.');
            document.getElementById('processingStatus').style.display = 'none';
        }
    };

    // Read CSV file as text
    reader.readAsText(file);
});

function parseCSV(text) {
    const lines = text.split('\n');
    const headers = lines[0].split(',').map(h => h.trim().toLowerCase());
    const data = [];

    for (let i = 1; i < lines.length; i++) {
        if (lines[i].trim() === '') continue;

        const values = lines[i].split(',');
        const row = {};

        headers.forEach((header, index) => {
            row[header] = values[index] ? values[index].trim() : '';
        });

        data.push(row);
    }

    return data;
}

function displayPreview() {
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = '';

    csvData.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.id = `row-${index}`;

        tr.innerHTML = `
            <td>${index + 1}</td>
            <td><span class="badge bg-secondary" id="status-${index}">Pending</span></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.meterno || ''}" onchange="updateRow(${index}, 'meterno', this.value)"></td>
            <td>
                <select class="form-control form-control-sm" onchange="updateRow(${index}, 'metermodel', this.value)">
                    <option value="prepaid" ${row.metermodel == 'prepaid' ? 'selected' : ''}>Prepaid</option>
                    <option value="postpaid" ${row.metermodel == 'postpaid' ? 'selected' : ''}>Postpaid</option>
                </select>
            </td>
            <td><input type="text" class="form-control form-control-sm" value="${row.accountno || ''}" onchange="updateRow(${index}, 'accountno', this.value)"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.transformer_id || ''}" onchange="updateRow(${index}, 'transformer_id', this.value)" placeholder="Optional"></td>
            <td>
                <select class="form-control form-control-sm" disabled>
                    <option value="0" ${row.isdualtariff == '0' ? 'selected' : ''}>No</option>
                    <option value="1" ${row.isdualtariff == '1' ? 'selected' : ''}>Yes</option>
                </select>
                <small class="text-muted">Set in spreadsheet</small>
            </td>
            <td>
                <select class="form-control form-control-sm" onchange="updateRow(${index}, 'oldsgc', this.value)">
                    <option value="999962" ${row.oldsgc == '999962' ? 'selected' : ''}>MOMAS Default</option>
                    <option value="600849" ${row.oldsgc == '600849' ? 'selected' : ''}>MOMAS System</option>
                </select>
            </td>
            <td>
                <select class="form-control form-control-sm" onchange="updateRow(${index}, 'newsgc', this.value)">
                    <option value="999962" ${row.newsgc == '999962' ? 'selected' : ''}>MOMAS Default</option>
                    <option value="600849" ${row.newsgc == '600849' ? 'selected' : ''}>MOMAS System</option>
                </select>
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <input type="text" class="form-control form-control-sm" value="${row.newtariffid || ''}" onchange="updateRow(${index}, 'newtariffid', this.value)" placeholder="New">
                    <input type="text" class="form-control form-control-sm" value="${row.oldtariffid || ''}" onchange="updateRow(${index}, 'oldtariffid', this.value)" placeholder="Old">
                </div>
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <input type="text" class="form-control form-control-sm" value="${row.newtariffdual || ''}" placeholder="New Gen" readonly style="background-color: #f8f9fa;">
                    <input type="text" class="form-control form-control-sm" value="${row.oldtariffdual || ''}" placeholder="Old Gen" readonly style="background-color: #f8f9fa;">
                </div>
                ${row.isdualtariff == '1' ? '<small class="text-muted">Set in spreadsheet</small>' : ''}
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <select class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                        <option value="999962" ${(row.newsgcdual || '999962') == '999962' ? 'selected' : ''}>MOMAS Default</option>
                        <option value="600849" ${row.newsgcdual == '600849' ? 'selected' : ''}>MOMAS System</option>
                    </select>
                    <select class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                        <option value="999962" ${(row.oldsgcdual || '999962') == '999962' ? 'selected' : ''}>MOMAS Default</option>
                        <option value="600849" ${row.oldsgcdual == '600849' ? 'selected' : ''}>MOMAS System</option>
                    </select>
                </div>
                ${row.isdualtariff == '1' ? '<small class="text-muted">Set in spreadsheet</small>' : ''}
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <select class="form-control form-control-sm" onchange="updateRow(${index}, 'krn1', this.value)">
                        <option value="STS6" ${row.krn1 == 'STS6' ? 'selected' : ''}>STS6</option>
                        <option value="STS" ${row.krn1 == 'STS' ? 'selected' : ''}>STS</option>
                    </select>
                    <select class="form-control form-control-sm" onchange="updateRow(${index}, 'krn2', this.value)">
                        <option value="STS6" ${row.krn2 == 'STS6' ? 'selected' : ''}>STS6</option>
                        <option value="STS" ${row.krn2 == 'STS' ? 'selected' : ''}>STS</option>
                    </select>
                </div>
            </td>
            <td>
                <select class="form-control form-control-sm" onchange="updateRow(${index}, 'needkct', this.value)">
                    <option value="0" ${row.needkct == '0' ? 'selected' : ''}>No</option>
                    <option value="1" ${row.needkct == '1' ? 'selected' : ''}>Yes</option>
                </select>
            </td>
            <td>
                <select class="form-control form-control-sm" onchange="updateRow(${index}, 'credittype', this.value)">
                    <option value="electricity" ${row.credittype == 'electricity' ? 'selected' : ''}>Electricity</option>
                    <option value="water" ${row.credittype == 'water' ? 'selected' : ''}>Water</option>
                    <option value="gas" ${row.credittype == 'gas' ? 'selected' : ''}>Gas</option>
                </select>
            </td>
            <td><span id="errors-${index}" class="text-danger small"></span></td>
            <td><button class="btn btn-sm btn-danger" onclick="deleteRow(${index})">Delete</button></td>
        `;

        tbody.appendChild(tr);
    });

    document.getElementById('processingStatus').style.display = 'none';
    document.getElementById('previewSection').style.display = 'block';

    // Update summary
    document.getElementById('validationSummary').textContent = `${csvData.length} rows loaded`;
    document.getElementById('validationSummary').className = 'badge bg-info';
}

function updateRow(index, field, value) {
    csvData[index][field] = value;
    // Clear validation status
    document.getElementById(`status-${index}`).textContent = 'Modified';
    document.getElementById(`status-${index}`).className = 'badge bg-warning';
    document.getElementById(`errors-${index}`).textContent = '';
}

function deleteRow(index) {
    document.getElementById(`row-${index}`).remove();
    csvData.splice(index, 1);
    // Refresh the table to fix row numbers
    displayPreview();
}

// Validation logic
document.getElementById('validateBtn').addEventListener('click', function() {
    validateAllRows();
});

async function validateAllRows() {
    document.getElementById('validateBtn').disabled = true;
    document.getElementById('validateBtn').textContent = 'Validating...';

    // Get existing meter numbers from database
    try {
        const response = await fetch('/api/get-existing-meters');
        existingMeterNumbers = await response.json();
    } catch (error) {
        console.error('Error fetching existing meters:', error);
    }

    let validCount = 0;
    let errorCount = 0;
    const meterNumbers = new Set();

    csvData.forEach((row, index) => {
        const errors = validateRow(row, index, meterNumbers);

        if (errors.length === 0) {
            document.getElementById(`status-${index}`).textContent = 'Valid';
            document.getElementById(`status-${index}`).className = 'badge bg-success';
            document.getElementById(`errors-${index}`).textContent = '';
            validCount++;
        } else {
            document.getElementById(`status-${index}`).textContent = 'Error';
            document.getElementById(`status-${index}`).className = 'badge bg-danger';
            document.getElementById(`errors-${index}`).textContent = errors.join(', ');
            errorCount++;
        }

        meterNumbers.add(row.meterno);
    });

    // Update summary and show results
    if (errorCount === 0) {
        document.getElementById('validationSummary').textContent = `All ${validCount} rows valid`;
        document.getElementById('validationSummary').className = 'badge bg-success';
        document.getElementById('saveBtn').style.display = 'inline-block';

        document.getElementById('validationResults').innerHTML = `
            <div class="alert alert-success">
                <strong>Validation Complete!</strong> All ${validCount} rows are valid and ready to save.
            </div>
        `;
    } else {
        document.getElementById('validationSummary').textContent = `${errorCount} errors found`;
        document.getElementById('validationSummary').className = 'badge bg-danger';
        document.getElementById('saveBtn').style.display = 'none';

        document.getElementById('validationResults').innerHTML = `
            <div class="alert alert-danger">
                <strong>Validation Failed!</strong> ${errorCount} rows have errors. Please fix them before saving.
            </div>
        `;
    }

    document.getElementById('validateBtn').disabled = false;
    document.getElementById('validateBtn').textContent = 'Validate All';
}

function validateRow(row, index, usedMeterNumbers) {
    const errors = [];

    // Required fields
    if (!row.meterno || row.meterno.trim() === '') {
        errors.push('Meter number required');
    }

    if (!row.metermodel || row.metermodel.trim() === '') {
        errors.push('Meter model required');
    } else if (!['prepaid', 'postpaid'].includes(row.metermodel.toLowerCase())) {
        errors.push('Meter model must be prepaid or postpaid');
    }

    if (!row.accountno || row.accountno.trim() === '') {
        errors.push('Account number required');
    }

    if (!['0', '1'].includes(row.isdualtariff)) {
        errors.push('Dual tariff must be 0 or 1');
    }

    // SGC validation
    if (row.oldsgc && !['999962', '600849'].includes(row.oldsgc)) {
        errors.push('Old SGC must be 999962 or 600849');
    }

    if (row.newsgc && !['999962', '600849'].includes(row.newsgc)) {
        errors.push('New SGC must be 999962 or 600849');
    }

    // KRN validation
    if (row.krn1 && !['STS6', 'STS'].includes(row.krn1)) {
        errors.push('KRN1 must be STS6 or STS');
    }

    if (row.krn2 && !['STS6', 'STS'].includes(row.krn2)) {
        errors.push('KRN2 must be STS6 or STS');
    }

    // Need KCT validation
    if (row.needkct && !['0', '1'].includes(row.needkct)) {
        errors.push('Need KCT must be 0 or 1');
    }

    // Credit type validation
    if (row.credittype && !['electricity', 'water', 'gas'].includes(row.credittype.toLowerCase())) {
        errors.push('Credit type must be electricity, water, or gas');
    }

    // Dual tariff logic validation
    if (row.isdualtariff === '0') {
        if (row.newtariffdual && row.newtariffdual.trim() !== '') {
            errors.push('New generator tariff should be empty when dual tariff is disabled');
        }
        if (row.oldtariffdual && row.oldtariffdual.trim() !== '') {
            errors.push('Old generator tariff should be empty when dual tariff is disabled');
        }
        if (row.newsgcdual && row.newsgcdual.trim() !== '') {
            errors.push('New generator SGC should be empty when dual tariff is disabled');
        }
        if (row.oldsgcdual && row.oldsgcdual.trim() !== '') {
            errors.push('Old generator SGC should be empty when dual tariff is disabled');
        }
    }

    // Generator SGC validation for dual tariff meters
    if (row.isdualtariff === '1') {
        if (row.newsgcdual && !['999962', '600849'].includes(row.newsgcdual)) {
            errors.push('New generator SGC must be 999962 or 600849');
        }
        if (row.oldsgcdual && !['999962', '600849'].includes(row.oldsgcdual)) {
            errors.push('Old generator SGC must be 999962 or 600849');
        }
    }

    // Check duplicates within batch
    if (row.meterno && usedMeterNumbers.has(row.meterno)) {
        errors.push('Duplicate meter number in file');
    }

    // Check existing in database
    if (row.meterno && existingMeterNumbers.includes(row.meterno)) {
        errors.push('Meter exists in database');
    }

    return errors;
}

// Save to database
document.getElementById('saveBtn').addEventListener('click', function() {
    saveToDB();
});

async function saveToDB() {
    if (!confirm('Save all valid meters to database? This action cannot be undone.')) {
        return;
    }

    // Use Blade to determine the user role and output a JavaScript variable.
    const userRole = {{ Auth::user()->role }};
    const userEstateId = {{ Auth::user()->estate_id ?? 'null' }}; // Use null if not set
    let estateId;

    if (userRole === 0) { // Now this is a pure JavaScript conditional
        estateId = document.getElementById('estateSelect').value;
        if (!estateId) {
            alert('Please select an estate first.');
            return;
        }
    } else {
        estateId = userEstateId;
    }

    document.getElementById('saveBtn').disabled = true;
    document.getElementById('saveBtn').textContent = 'Saving...';

    try {
        const response = await fetch('/admin/bulk-save-meters', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // 
            },
            body: JSON.stringify({
                meters: csvData,
                estate_id: estateId
                
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Meters saved successfully!');
            window.location.href = '/admin/meter-list';
        } else {
            alert('Error saving meters: ' + result.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    document.getElementById('saveBtn').disabled = false;
    document.getElementById('saveBtn').textContent = 'Save to Database';
}
</script>

@endsection