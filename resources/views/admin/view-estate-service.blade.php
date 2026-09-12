@extends('layouts.admin')

@section('title', 'Admin - View Artisan (#ART-001) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Manage Artisan: David Adeleke (#ART-001)</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Inspect service performance, active job requests, and update artisan parameters.</p>
          </div>
          <a href="{{ route('admin.estate-service') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Services
          </a>
        </div>

        <!-- 4-Card Horizontal Metric Row -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              </div>
              <span>Artisan ID</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">#ART-001</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">David Adeleke</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <span>Service Category</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.2rem; color: #00A859;">Certified Electrician</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">EVE Estate Assigned</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
              <span>Rating & Jobs</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">4.9 / 5.0 ⭐</div>
            <div style="font-size: 0.78rem; color: #059669; font-weight: 600;">142 Completed Tasks</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg>
              </div>
              <span>Status</span>
            </div>
            <div style="margin-top: 4px;"><span class="badge-status resolved" style="font-size: 0.85rem; padding: 5px 14px;">Active</span></div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600; margin-top: 4px;">Available for Dispatch</div>
          </div>
        </div>

        <!-- Editable Form Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Artisan Parameters & Information</div>
          </div>

          <form action="{{ route('admin.estate-service') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
              <div class="admin-field-group">
                <label class="admin-field-label">Provider ID</label>
                <input type="text" class="admin-field-input" value="#ART-001" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Artisan Name</label>
                <input type="text" class="admin-field-input" value="David Adeleke" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Service Category</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Certified Electrician" selected>Certified Electrician</option>
                  <option value="Plumbing Specialist">Plumbing Specialist</option>
                  <option value="HVAC & AC Technician">HVAC & AC Technician</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Phone Number</label>
                <input type="text" class="admin-field-input" value="+2348039201948" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate" selected>EVE Estate</option>
                  <option value="Hope Estate">Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">License Reference</label>
                <input type="text" class="admin-field-input" value="NATE-CERT-77102">
              </div>
            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Changes
              </button>
              <a href="{{ route('admin.estate-service') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
