@extends('layouts.main')
@section('content')

    @php
        $displaysAtValue = old('displays_at')
            ? \Carbon\Carbon::parse(old('displays_at'))->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');
        $expiresAtValue = old('expires_at')
            ? \Carbon\Carbon::parse(old('expires_at'))->format('Y-m-d\TH:i')
            : '';
    @endphp

    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Create Alert</h4>
                    <p class="text-muted fs-13 mb-0">Publish an alert and choose the customers who should receive it.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('alerts.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" class="me-1"></i> Back to Alerts
                    </a>
                </div>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('alerts.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-lg-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title text-black mb-0">Alert Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Alert Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="e.g. Scheduled maintenance notice" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Image URL</label>
                                    <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}" class="form-control" placeholder="https://example.com/alert-image.jpg">
                                </div>

                                <div class="mb-3 d-none" id="image_preview_wrapper">
                                    <label class="form-label fw-medium">Image Preview</label>
                                    <div class="border rounded p-2 bg-light">
                                        <img src="" id="image_preview" alt="Alert preview" class="img-fluid rounded" style="max-height: 220px;">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
                                        <select name="priority" class="form-select" required>
                                            @foreach($priorities as $priority => $weight)
                                                <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>
                                                    {{ ucfirst($priority) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium d-block">Urgent Alert</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" role="switch" name="is_urgent" value="1" id="is_urgent" @checked(old('is_urgent'))>
                                            <label class="form-check-label" for="is_urgent">Mark this alert as urgent</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Displays At <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="displays_at" value="{{ $displaysAtValue }}" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Expires At</label>
                                        <input type="datetime-local" name="expires_at" value="{{ $expiresAtValue }}" class="form-control">
                                        <div class="form-text">Leave blank for an alert that never expires.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title text-black mb-0">Target Group</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" name="all_users" value="1" id="all_users_switch" @checked(old('all_users'))>
                                    <label class="form-check-label fw-medium" for="all_users_switch">
                                        All users
                                        <span class="d-block text-muted fs-12 fw-normal">When enabled, all estate, customer and meter targeting is disabled.</span>
                                    </label>
                                </div>

                                <div id="target-fieldset">
                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Estates</label>
                                        <div class="border rounded p-2" style="max-height: 190px; overflow-y: auto;">
                                            @forelse($estates as $estate)
                                                <div class="form-check">
                                                    <input class="form-check-input target-input" type="checkbox"
                                                           name="estates[]" value="{{ $estate->id }}"
                                                           id="estate-{{ $estate->id }}"
                                                           @checked(in_array($estate->id, old('estates', [])))>
                                                    <label class="form-check-label" for="estate-{{ $estate->id }}">
                                                        {{ $estate->title }}
                                                    </label>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0 fs-13">No estates available.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium d-flex justify-content-between">
                                            <span>Customers</span>
                                            <span class="text-muted fs-12 fw-normal" id="customer_selected_count">0 selected</span>
                                        </label>

                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                                Select customers
                                            </button>
                                            <div class="dropdown-menu w-100 p-2" style="max-height: 280px; overflow-y: auto;">
                                                <input type="text" id="customer_search" class="form-control form-control-sm mb-2" placeholder="Search by name, email or phone...">
                                                <div id="customer_list">
                                                    @forelse($customers as $customer)
                                                        @php
                                                            $customerName = trim($customer->first_name.' '.$customer->last_name);
                                                            $customerSearch = strtolower($customerName.' '.$customer->email.' '.$customer->phone);
                                                        @endphp
                                                        <div class="form-check customer-option" data-search="{{ $customerSearch }}">
                                                            <input class="form-check-input customer-checkbox target-input" type="checkbox"
                                                                   name="users[]" value="{{ $customer->id }}"
                                                                   id="customer-{{ $customer->id }}"
                                                                   @checked(in_array($customer->id, old('users', [])))>
                                                            <label class="form-check-label" for="customer-{{ $customer->id }}">
                                                                <span class="fw-medium">{{ $customerName }}</span>
                                                                <span class="d-block text-muted fs-12">{{ $customer->email }}{{ $customer->phone ? ' | '.$customer->phone : '' }}</span>
                                                            </label>
                                                        </div>
                                                    @empty
                                                        <p class="text-muted mb-0 fs-13">No customers available.</p>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Meter Numbers</label>
                                        <input type="text" name="meter_numbers" id="meter_numbers" value="{{ old('meter_numbers') }}" class="form-control" placeholder="e.g. 01423456789, 01423456790">
                                        <div class="form-text">Enter multiple meter numbers separated by commas. The owners of these meters will receive the alert.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('alerts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="send" class="me-1"></i> Create Alert
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const allUsersSwitch = document.getElementById('all_users_switch');
            const targetFieldset = document.getElementById('target-fieldset');
            const targetInputs = targetFieldset.querySelectorAll('input, select, textarea, button');

            function toggleTargetFields() {
                const disabled = allUsersSwitch.checked;

                targetInputs.forEach(function (input) {
                    input.disabled = disabled;
                });

                targetFieldset.classList.toggle('opacity-50', disabled);
            }

            allUsersSwitch.addEventListener('change', toggleTargetFields);
            toggleTargetFields();

            const customerSearch = document.getElementById('customer_search');
            const customerOptions = document.querySelectorAll('.customer-option');
            const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
            const customerSelectedCount = document.getElementById('customer_selected_count');

            function updateCustomerCount() {
                const selected = document.querySelectorAll('.customer-checkbox:checked').length;
                customerSelectedCount.textContent = selected + ' selected';
            }

            customerCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', updateCustomerCount);
            });
            updateCustomerCount();

            customerSearch.addEventListener('input', function () {
                const search = this.value.toLowerCase().trim();

                customerOptions.forEach(function (option) {
                    option.classList.toggle('d-none', search !== '' && !option.dataset.search.includes(search));
                });
            });

            const imageUrl = document.getElementById('image_url');
            const imagePreview = document.getElementById('image_preview');
            const imagePreviewWrapper = document.getElementById('image_preview_wrapper');

            function updateImagePreview() {
                const value = imageUrl.value.trim();

                if (value !== '' && /^https?:\/\//i.test(value)) {
                    imagePreview.src = value;
                    imagePreviewWrapper.classList.remove('d-none');
                } else {
                    imagePreview.removeAttribute('src');
                    imagePreviewWrapper.classList.add('d-none');
                }
            }

            imageUrl.addEventListener('input', updateImagePreview);
            updateImagePreview();
        });
    </script>

@endsection
