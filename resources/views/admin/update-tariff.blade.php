@extends('layouts.admin')

@section('title', 'Admin - Update Tariff Rate (#TRF-A1) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div class="admin-section-title">Update Tariff Rate — #TRF-A1 (Residential Band A)</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Update electricity tariff rates, VAT tax percentage, or DISCO provider classifications</p>
          </div>
          <a href="{{ route('admin.tariff') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Tariff List
          </a>
        </div>

        <!-- Update Tariff Form Card -->
        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="admin-card-title">Update Tariff Rate (#TRF-A1)</div>
            <span class="badge-status resolved">Active</span>
          </div>

          <form action="{{ route('admin.tariff') }}" method="GET" style="padding: 28px;">
            <div class="admin-fields-grid" style="background: transparent; border: none; padding: 0; gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Code</label>
                <input type="text" class="admin-field-input" value="#TRF-A1" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Classification Name *</label>
                <input type="text" class="admin-field-input" value="Residential Band A" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Current Rate per kWh</label>
                <input type="text" class="admin-field-input" value="₦209.50 / kWh" readonly style="background: #ECFDF5; color: #00A859; font-weight: 800;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">New Rate per kWh (₦) *</label>
                <input type="text" class="admin-field-input" value="₦209.50 / kWh" required style="font-weight: 700; color: #00A859;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">DISCO Electricity Provider</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="IKEDC" selected>IKEDC (Ikeja Electric)</option>
                  <option value="EKEDC">EKEDC (Eko Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">VAT Tax (%)</label>
                <input type="text" class="admin-field-input" value="7.5%">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Effective Revision Date</label>
                <input type="date" class="admin-field-input" value="2026-09-07">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Regulatory Approval Code</label>
                <input type="text" class="admin-field-input" value="NERC/REG/2026/049">
              </div>

            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9; flex-wrap: wrap; gap: 16px;">
              <div style="display: flex; gap: 16px; align-items: center;">
                <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                  Update Tariff Rate
                </button>
                <a href="{{ route('admin.tariff') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                  Cancel
                </a>
              </div>

              <button type="button" class="btn-action-close" onclick="alert('Tariff plan deactivated'); location.href='admin-tariff.html';" style="padding: 12px 24px; font-size: 0.88rem;">
                Deactivate Tariff
              </button>
            </div>
          </form>
        </div>
@endsection
