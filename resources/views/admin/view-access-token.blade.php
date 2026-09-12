@extends('layouts.admin')

@section('title', 'Admin - Access Code Details (4415838) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Access Code Details: 4415838</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Inspect security token parameters, host resident info, and invalidate active passes.</p>
          </div>
          <a href="{{ route('admin.access-token') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Access Codes
          </a>
        </div>

        <!-- 4-Card Horizontal Executive Metric Row -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-2 2l-2 2m7-2l-2-2m-2 2l-2-2M3 13.64V21h7.36l10-10L13 3.64l-10 10z"/></svg>
              </div>
              <span>Access Code</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.55rem; color: #00A859; letter-spacing: 2px;">4415838</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Single Entry Pass</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <span>Visitor Name</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.3rem;">Adejimi Tolulope</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Host: James Doe</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <span>Validity</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.2rem;">Today, 23:59</div>
            <div style="font-size: 0.78rem; color: #D97706; font-weight: 600;">Hope Estate Main Gate</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg>
              </div>
              <span>Code Status</span>
            </div>
            <div style="margin-top: 4px;"><span class="badge-status resolved" style="font-size: 0.85rem; padding: 5px 14px;">Valid</span></div>
            <div style="font-size: 0.78rem; color: #059669; font-weight: 600; margin-top: 4px;">Gate Verification Ready</div>
          </div>
        </div>

        <!-- Quick Revoke Action Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Security Actions</div>
          </div>
          <div style="padding: 24px;">
            <button type="button" class="btn-action-escalate" style="padding: 10px 24px; font-weight: 600;" onclick="handleRevokeCode()">
              Revoke & Invalidate Access Code
            </button>
          </div>
        </div>
@endsection
