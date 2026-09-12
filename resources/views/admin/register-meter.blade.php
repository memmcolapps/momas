@extends('layouts.admin')

@section('title', 'Admin - Register Meter - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Register New Meter</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Fill out the form below to add a new smart electricity meter into the MomasPay inventory</p>
          </div>
          <a href="{{ route('admin.meter') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Installed Meters
          </a>
        </div>

        <!-- Register Meter Form Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Meter Details & Assignment</div>
          </div>

          <form action="{{ route('admin.meter') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Meter Number *</label>
                <input type="text" class="admin-field-input" value="04218849355" required style="font-weight: 700; color: #00A859;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Customer Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Samuel Adebayo" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Meter Phase & Type *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Single Phase PREPAID" selected>Single Phase PREPAID</option>
                  <option value="Three Phase PREPAID">Three Phase PREPAID</option>
                  <option value="Single Phase POSTPAID">Single Phase POSTPAID</option>
                  <option value="Three Phase POSTPAID">Three Phase POSTPAID</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">DISCO Electricity Provider *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="IKEDC" selected>IKEDC (Ikeja Electric)</option>
                  <option value="EKEDC">EKEDC (Eko Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                  <option value="IBEDC">IBEDC (Ibadan Electric)</option>
                  <option value="PHED">PHED (Port Harcourt Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate" selected>EVE Estate</option>
                  <option value="Hope Estate">Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                  <option value="Gracefield Estate Phase 2">Gracefield Estate Phase 2</option>
                  <option value="Royal Palms Estate">Royal Palms Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Plan / Rate</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="R2-Single Phase (₦75.00/kWh)" selected>R2 - Single Phase (₦75.00 / kWh)</option>
                  <option value="R3-Three Phase (₦85.00/kWh)">R3 - Three Phase (₦85.00 / kWh)</option>
                  <option value="Band A (₦209.50/kWh)">Band A - Premium (₦209.50 / kWh)</option>
                  <option value="Commercial C1 (₦110.00/kWh)">Commercial C1 (₦110.00 / kWh)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Initial Credit Balance (₦)</label>
                <input type="text" class="admin-field-input" placeholder="e.g. ₦ 5,000.00" value="₦ 5,000.00">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">STS Key Revision / SGC</label>
                <input type="text" class="admin-field-input" value="SGC 990123 / Key Rev 1" placeholder="e.g. SGC 990123">
              </div>

              <div class="admin-field-group" style="grid-column: 1 / -1;">
                <label class="admin-field-label">Installation Address / Location</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Flat 4B, Block 12, EVE Estate, Ikeja, Lagos" value="Flat 4B, Block 12, EVE Estate, Ikeja, Lagos">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Commissioning Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Disconnected">Disconnected</option>
                  <option value="Pending">Pending Commissioning</option>
                </select>
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Register Meter
              </button>
              <a href="{{ route('admin.meter') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
