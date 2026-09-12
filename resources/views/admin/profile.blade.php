@extends('layouts.admin')

@section('title', 'Admin - Profile & Account Settings - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Admin Account Profile</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Manage personal credentials, executive access roles, and 2FA security controls</p>
          </div>
          <a href="{{ route('admin.dashboard') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Dashboard
          </a>
        </div>

        <!-- Profile Banner Header Card -->
        <div class="admin-content-card" style="margin-top: 24px; padding: 28px; background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 20px;">
              <div style="position: relative;">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80" alt="Admin Avatar" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid #00C868; box-shadow: 0 6px 16px rgba(0, 168, 89, 0.2);">
                <button type="button" style="position: absolute; bottom: 0; right: 0; background: #00A859; color: #FFF; border: 2px solid #FFF; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Change Profile Picture">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </button>
              </div>
              <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                  <h2 style="font-weight: 800; font-size: 1.35rem; color: #0F172A; margin: 0;">James Doe</h2>
                  <span class="badge-status resolved" style="padding: 4px 12px; font-size: 0.76rem;">Super Administrator</span>
                </div>
                <div style="font-size: 0.86rem; color: #64748B; margin-top: 4px; font-weight: 600;">admin@momaspay.com · Executive ID: ADM-8801</div>
                <div style="font-size: 0.78rem; color: #059669; font-weight: 700; margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                  <span style="width: 8px; height: 8px; border-radius: 50%; background: #00C868; display: inline-block;"></span>
                  Active Session · 2FA Authentication Enabled
                </div>
              </div>
            </div>

            <button type="button" class="btn-action-view" style="padding: 10px 24px; font-size: 0.85rem;" onclick="MomasAlert.success('Avatar Updated!', 'Your profile image has been refreshed successfully.')">
              Upload New Photo
            </button>
          </div>
        </div>

        <!-- Form Card: Personal Details -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Personal & Contact Information</div>
          </div>

          <form action="{{ route('admin.profile') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
              <div class="admin-field-group">
                <label class="admin-field-label">Full Name *</label>
                <input type="text" class="admin-field-input" value="James Doe" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Official Email Address *</label>
                <input type="email" class="admin-field-input" value="admin@momaspay.com" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Mobile Phone Number</label>
                <input type="tel" class="admin-field-input" value="+234 803 123 4567" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Employee / Staff ID</label>
                <input type="text" class="admin-field-input" value="ADM-8801" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Department / Unit</label>
                <input type="text" class="admin-field-input" value="Executive Utility Management" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">System Access Tier</label>
                <input type="text" class="admin-field-input" value="Level 1 - Root Super Admin" readonly style="background: #ECFDF5; color: #00A859; font-weight: 700;">
              </div>
            </div>

            <!-- Security & Password Reset Section -->
            <div style="margin-top: 36px; padding-top: 28px; border-top: 1px solid #F1F5F9;">
              <div style="font-weight: 800; font-size: 1.1rem; color: #0F172A; margin-bottom: 16px;">Security & Password Management</div>
              
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div class="admin-field-group">
                  <label class="admin-field-label">Current Password</label>
                  <input type="password" class="admin-field-input" placeholder="••••••••••••">
                </div>

                <div class="admin-field-group">
                  <label class="admin-field-label">New Password</label>
                  <input type="password" class="admin-field-input" placeholder="Enter new password">
                </div>

                <div class="admin-field-group">
                  <label class="admin-field-label">Confirm New Password</label>
                  <input type="password" class="admin-field-input" placeholder="Re-type new password">
                </div>
              </div>
            </div>

            <!-- Two Factor Authentication Toggle -->
            <div style="margin-top: 28px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
              <div>
                <div style="font-weight: 800; font-size: 0.95rem; color: #0F172A;">Two-Factor Authentication (2FA)</div>
                <div style="font-size: 0.8rem; color: #64748B; margin-top: 4px;">Require an OTP code sent via SMS/Email during admin logins</div>
              </div>
              <span class="badge-status resolved" style="padding: 6px 16px; font-size: 0.82rem;">Enabled (Active)</span>
            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Profile Changes
              </button>
              <a href="{{ route('admin.dashboard') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
