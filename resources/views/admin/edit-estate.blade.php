@extends('layouts.admin')

@section('title', 'Admin - Edit Estate (#EST-01) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div class="admin-section-title">Edit Estate — EVE Estate (#EST-01)</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Update estate details, tariff rates, manager contacts, or status settings</p>
          </div>
          <a href="{{ route('admin.estate') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Estates
          </a>
        </div>

        <!-- Edit Estate Form Card -->
        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="admin-card-title">Update Estate Details (#EST-01)</div>
            <span class="badge-status resolved">Active</span>
          </div>

          <form action="{{ route('admin.estate') }}" method="GET" style="padding: 28px;">
            <div class="admin-fields-grid" style="background: transparent; border: none; padding: 0; gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Estate ID</label>
                <input type="text" class="admin-field-input" value="#EST-01" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Estate Name *</label>
                <input type="text" class="admin-field-input" value="EVE Estate" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Location / Address *</label>
                <input type="text" class="admin-field-input" value="Lekki Phase 1, Lagos" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">DISCO Electricity Provider</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EKEDC" selected>EKEDC (Eko Electric)</option>
                  <option value="IKEDC">IKEDC (Ikeja Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Total Connected Meters</label>
                <input type="text" class="admin-field-input" value="1,240" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Rate (₦ / kWh)</label>
                <input type="text" class="admin-field-input" value="₦68.50 / kWh">
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
                <input type="text" class="admin-field-input" value="Adejimi Tolulope">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Contact Phone Number</label>
                <input type="text" class="admin-field-input" value="+234 802 345 6789">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Contact Email Address</label>
                <input type="email" class="admin-field-input" value="management@eveestate.ng">
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9; flex-wrap: wrap; gap: 16px;">
              <div style="display: flex; gap: 16px; align-items: center;">
                <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                  Update Estate
                </button>
                <a href="{{ route('admin.estate') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                  Cancel
                </a>
              </div>

              <button type="button" class="btn-action-close" onclick="alert('Estate deactivated successfully'); location.href='admin-estate.html';" style="padding: 12px 24px; font-size: 0.88rem;">
                Deactivate Estate
              </button>
            </div>
          </form>
        </div>
@endsection
