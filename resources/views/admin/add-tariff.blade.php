@extends('layouts.admin')

@section('title', 'Admin - Create Tariff Plan - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div class="admin-section-title">Create New Tariff Plan</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Configure a new electricity billing tariff rate structure on MomasPay</p>
          </div>
          <a href="{{ route('admin.tariff') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Tariff List
          </a>
        </div>

        <!-- Add Tariff Form Card -->
        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Tariff Configuration Form</div>
          </div>

          <form action="{{ route('admin.tariff') }}" method="GET" style="padding: 28px;">
            <div class="admin-fields-grid" style="background: transparent; border: none; padding: 0; gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Code</label>
                <input type="text" class="admin-field-input" value="#TRF-C1" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Classification Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Industrial Band C" value="Industrial Band C" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Rate per kWh (₦) *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. ₦165.00 / kWh" value="₦165.00 / kWh" required style="font-weight: 700; color: #00A859;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">DISCO Electricity Provider</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="IKEDC">IKEDC (Ikeja Electric)</option>
                  <option value="EKEDC" selected>EKEDC (Eko Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                  <option value="IBEDC">IBEDC (Ibadan Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">VAT Tax (%)</label>
                <input type="text" class="admin-field-input" value="7.5%">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Fixed Service Fee (₦)</label>
                <input type="text" class="admin-field-input" value="₦100.00">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Effective Start Date</label>
                <input type="date" class="admin-field-input" value="2026-09-01">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">NERC Regulatory Ref</label>
                <input type="text" class="admin-field-input" placeholder="e.g. NERC/REG/2026/088" value="NERC/REG/2026/088">
              </div>

            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Tariff Plan
              </button>
              <a href="{{ route('admin.tariff') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
