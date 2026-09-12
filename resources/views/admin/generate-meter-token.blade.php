@extends('layouts.admin')

@section('title', 'Admin - Generate Meter Token - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Generate STS Meter Token</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Vend a 20-digit electricity credit token or clear tamper token for smart meters</p>
          </div>
          <a href="{{ route('admin.meter-token') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Token Logs
          </a>
        </div>

        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Token Vending Form</div>
          </div>

          <form action="{{ route('admin.meter-token') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Token Log ID</label>
                <input type="text" class="admin-field-input" value="#TKN-8843" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Meter Number *</label>
                <select class="admin-field-input" style="cursor: pointer; font-weight: 700; color: #00A859;">
                  <option value="04218849201" selected>04218849201 (James Doe - EVE Estate)</option>
                  <option value="04218849288">04218849288 (Mary Deji - Hope Estate)</option>
                  <option value="04218849312">04218849312 (Jake Leonard - Kodak Estate)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Token Type *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Credit Token" selected>Credit Token (Power Recharge)</option>
                  <option value="Clear Tamper Token">Clear Tamper Token (Reset Cover Tamper)</option>
                  <option value="Key Change Token">Key Change Token (SGC Key Upgrade)</option>
                  <option value="Clear Credit Token">Clear Credit Token (Reset Meter Credit)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Purchase Amount (₦) *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. ₦ 10,000.00" value="₦ 10,000.00" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Calculated Units (kWh)</label>
                <input type="text" class="admin-field-input" value="133.33 kWh" readonly style="background: #ECFDF5; color: #00A859; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Vending Channel</label>
                <input type="text" class="admin-field-input" value="Admin Executive Console" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group" style="grid-column: 1 / -1;">
                <label class="admin-field-label">Generated 20-Digit STS Token Code</label>
                <input type="text" class="admin-field-input" value="3819-4412-9901-5520-8814" readonly style="background: #F0FDF4; color: #00A859; font-weight: 800; font-size: 1.1rem; letter-spacing: 2px;">
              </div>

            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Vend Token & Dispatch SMS
              </button>
              <a href="{{ route('admin.meter-token') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
