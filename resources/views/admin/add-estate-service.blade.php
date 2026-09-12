@extends('layouts.admin')

@section('title', 'Admin - Add Artisan Service Provider - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Register New Artisan Service Provider</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Add a verified electrician, plumber, or artisan into the estate service directory</p>
          </div>
          <a href="{{ route('admin.estate-service') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Services
          </a>
        </div>

        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Artisan Registration Details</div>
          </div>

          <form action="{{ route('admin.estate-service') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Provider ID</label>
                <input type="text" class="admin-field-input" value="#ART-003" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Artisan Full Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Samuel Opeyemi" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Service Category *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Certified Electrician" selected>Certified Electrician</option>
                  <option value="Plumbing Specialist">Plumbing Specialist</option>
                  <option value="HVAC & AC Technician">HVAC & AC Technician</option>
                  <option value="Solar & Inverter Expert">Solar & Inverter Expert</option>
                  <option value="Security Systems">Security Systems Tech</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Contact Phone Number *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. +234 803 456 7890" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Email Address</label>
                <input type="email" class="admin-field-input" placeholder="e.g. artisan@services.ng">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate" selected>EVE Estate</option>
                  <option value="Hope Estate">Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                  <option value="Gracefield Estate Phase 2">Gracefield Estate Phase 2</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Certification Ref / License</label>
                <input type="text" class="admin-field-input" placeholder="e.g. NATE-CERT-88401" value="NATE-CERT-88401">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Service Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Under Audit">Under Audit</option>
                </select>
              </div>

            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Register Artisan
              </button>
              <a href="{{ route('admin.estate-service') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
