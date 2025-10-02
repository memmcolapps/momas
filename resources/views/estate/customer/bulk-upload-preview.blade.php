@extends('layouts.main')
@section('content')

<div class="content">
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Bulk Customer Upload - Preview & Validate</h4>
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

                        <div class="mb-3">
                            <label class="form-label">Select CSV File (Max 100 rows)</label>
                            <input type="file" id="csvFile" class="form-control" accept=".csv,.xlsx" />
                            <small class="form-text text-muted">
                                Required columns: first_name, last_name, email, phone, meterno
                                <br>Optional columns: address, city, state, house_no, account_no
                                <br><strong>Note:</strong> Meters will be automatically assigned if they exist and are unassigned in your estate.
                                <a href="/asset/customer_upload_sample.csv" download class="ms-2">Download Sample</a>
                            </small>
                        </div>
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
                            <table class="table table-bordered table-sm" id="previewTable">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Status</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Meter No</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>House No</th>
                                        <th>Account No</th>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
let csvData = [];
let existingCustomers = [];
let availableMeters = [];
let assignedMeters = [];

document.getElementById('csvFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Show processing status
    document.getElementById('processingStatus').style.display = 'block';
    document.getElementById('previewSection').style.display = 'none';

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            let data;
            const fileName = file.name.toLowerCase();

            if (fileName.endsWith('.csv')) {
                // Parse CSV
                data = parseCSV(e.target.result);
            } else if (fileName.endsWith('.xlsx') || fileName.endsWith('.xls')) {
                // Parse Excel
                const workbook = XLSX.read(e.target.result, { type: 'binary' });
                const sheetName = workbook.SheetNames[0];
                const sheet = workbook.Sheets[sheetName];
                data = XLSX.utils.sheet_to_json(sheet);
            }

            // Limit to 100 rows
            if (data.length > 100) {
                data = data.slice(0, 100);
                alert('File limited to first 100 rows for performance.');
            }

            csvData = data;
            displayPreview();

        } catch (error) {
            alert('Error reading file: ' + error.message);
            document.getElementById('processingStatus').style.display = 'none';
        }
    };

    if (file.name.toLowerCase().endsWith('.csv')) {
        reader.readAsText(file);
    } else {
        reader.readAsBinaryString(file);
    }
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
            <td><input type="text" class="form-control form-control-sm" value="${row.first_name || ''}" onchange="updateRow(${index}, 'first_name', this.value)"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.last_name || ''}" onchange="updateRow(${index}, 'last_name', this.value)"></td>
            <td><input type="email" class="form-control form-control-sm" value="${row.email || ''}" onchange="updateRow(${index}, 'email', this.value)"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.phone || ''}" onchange="updateRow(${index}, 'phone', this.value)"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.meterno || ''}" onchange="updateRow(${index}, 'meterno', this.value)" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.address || ''}" onchange="updateRow(${index}, 'address', this.value)" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.city || ''}" onchange="updateRow(${index}, 'city', this.value)" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.state || ''}" onchange="updateRow(${index}, 'state', this.value)" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.house_no || ''}" onchange="updateRow(${index}, 'house_no', this.value)" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" value="${row.account_no || ''}" onchange="updateRow(${index}, 'account_no', this.value)" placeholder="Optional"></td>
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

    // Get estate ID
    const userRole = {{ Auth::user()->role }};
    let estateId;

    if (userRole === 0) {
        estateId = document.getElementById('estateSelect').value;
        if (!estateId) {
            alert('Please select an estate first.');
            document.getElementById('validateBtn').disabled = false;
            document.getElementById('validateBtn').textContent = 'Validate All';
            return;
        }
    } else {
        estateId = {{ Auth::user()->estate_id ?? 'null' }};
    }

    // Get existing customers and meters from database
    try {
        const [customersResponse, assignedResponse] = await Promise.all([
            fetch(`/admin/estate/get-existing-customers?estate_id=${estateId}`),
            fetch(`/admin/estate/get-assigned-meters?estate_id=${estateId}`)
        ]);

        existingCustomers = await customersResponse.json();
        assignedMeters = await assignedResponse.json();
    } catch (error) {
        console.error('Error fetching validation data:', error);
    }

    let validCount = 0;
    let errorCount = 0;
    const usedEmails = new Set();
    const usedPhones = new Set();
    const usedMeters = new Set();

    csvData.forEach((row, index) => {
        const errors = validateRow(row, index, usedEmails, usedPhones, usedMeters);

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

        // Track used values
        if (row.email) usedEmails.add(row.email);
        if (row.phone) usedPhones.add(row.phone);
        if (row.meterno) usedMeters.add(row.meterno);
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

function validateRow(row, index, usedEmails, usedPhones, usedMeters) {
    const errors = [];

    // Required fields
    if (!row.first_name || row.first_name.trim() === '') {
        errors.push('First name required');
    }

    if (!row.last_name || row.last_name.trim() === '') {
        errors.push('Last name required');
    }

    if (!row.email || row.email.trim() === '') {
        errors.push('Email required');
    } else {
        // Email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(row.email)) {
            errors.push('Invalid email format');
        }

        // Check duplicate email in database
        if (existingCustomers.some(c => c.email === row.email)) {
            errors.push('Email exists in database');
        }

        // Check duplicate email in CSV
        if (usedEmails.has(row.email)) {
            errors.push('Duplicate email in file');
        }
    }

    if (!row.phone || row.phone.trim() === '') {
        errors.push('Phone required');
    } else {
        // Check duplicate phone in database
        if (existingCustomers.some(c => c.phone === row.phone)) {
            errors.push('Phone exists in database');
        }

        // Check duplicate phone in CSV
        if (usedPhones.has(row.phone)) {
            errors.push('Duplicate phone in file');
        }
    }

    // Meter validation (optional but must be valid if provided)
    if (row.meterno && row.meterno.trim() !== '') {
        // Check if meter is already assigned
        const assignedMeter = assignedMeters.find(m => m.meterNo === row.meterno);
        if (assignedMeter) {
            errors.push(`Meter assigned to ${assignedMeter.assigned_to}`);
        }

        // Check duplicate meter in CSV
        if (usedMeters.has(row.meterno)) {
            errors.push('Duplicate meter in file');
        }
    }

    return errors;
}

// Save to database
document.getElementById('saveBtn').addEventListener('click', function() {
    saveToDB();
});

async function saveToDB() {
    if (!confirm('Save all valid customers to database? This action cannot be undone.')) {
        return;
    }

    // Get estate ID
    const userRole = {{ Auth::user()->role }};
    const userEstateId = {{ Auth::user()->estate_id ?? 'null' }};
    let estateId;

    if (userRole === 0) {
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
        const response = await fetch('/admin/estate/bulk-save-customers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                customers: csvData,
                estate_id: estateId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(`Success! ${result.saved_count} customers saved successfully.`);
            window.location.reload();
        } else {
            alert('Error saving customers: ' + result.message);
            if (result.errors && result.errors.length > 0) {
                console.error('Errors:', result.errors);
            }
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    document.getElementById('saveBtn').disabled = false;
    document.getElementById('saveBtn').textContent = 'Save to Database';
}
</script>

@endsection
