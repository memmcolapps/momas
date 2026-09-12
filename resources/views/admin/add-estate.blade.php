@extends('layouts.admin')

@section('title', 'Admin - Add New Estate - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div class="admin-section-title">Add New Estate</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Fill out the form below to register a new estate on the MomasPay platform</p>
          </div>
          <a href="{{ route('admin.estate') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Estates
          </a>
        </div>

        <!-- Add Estate Form Card -->
        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Estate Registration Form</div>
          </div>

          <form action="{{ route('admin.estate') }}" method="GET" style="padding: 28px;">
            <div class="admin-fields-grid" style="background: transparent; border: none; padding: 0; gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Estate ID</label>
                <input type="text" class="admin-field-input" value="#EST-04" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Estate Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Gracefield Estate Phase 2" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Location / Address *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Victoria Island Extension, Lagos" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">DISCO Electricity Provider</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="IKEDC">IKEDC (Ikeja Electric)</option>
                  <option value="EKEDC" selected>EKEDC (Eko Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                  <option value="IBEDC">IBEDC (Ibadan Electric)</option>
                  <option value="PHED">PHED (Port Harcourt Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Initial Meters Count</label>
                <input type="number" class="admin-field-input" placeholder="e.g. 500" value="100">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Rate (₦ / kWh)</label>
                <input type="text" class="admin-field-input" placeholder="e.g. ₦75.00 / kWh" value="₦75.00 / kWh">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Estate Manager Name</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Babatunde Johnson">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Contact Phone Number</label>
                <input type="text" class="admin-field-input" placeholder="e.g. +234 803 123 4567">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Contact Email Address</label>
                <input type="email" class="admin-field-input" placeholder="e.g. manager@estate.ng">
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Estate
              </button>
              <a href="{{ route('admin.estate') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
