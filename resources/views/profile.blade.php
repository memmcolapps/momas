@extends('layouts.app')

@section('title', 'My Profile - MomasPay')
@section('body_class', '')

@section('content')
<!-- Mobile Screen Container -->
  <div class="page-container" style="padding-bottom: 90px;">
    
    <!-- iOS Status Bar -->
    <div class="status-bar">
      <span class="status-time">9:41</span>
      <div class="status-icons">
        <svg width="17" height="11" viewBox="0 0 17 11" fill="currentColor">
          <rect x="0" y="8" width="3" height="3" rx="0.5"/>
          <rect x="4.5" y="5.5" width="3" height="5.5" rx="0.5"/>
          <rect x="9" y="3" width="3" height="8" rx="0.5"/>
          <rect x="13.5" y="0" width="3" height="11" rx="0.5"/>
        </svg>
        <svg width="16" height="11" viewBox="0 0 16 11" fill="currentColor">
          <path d="M8 11C8.75 11 9.35 10.4 9.35 9.65C9.35 8.9 8.75 8.3 8 8.3C7.25 8.3 6.65 8.9 6.65 9.65C6.65 10.4 7.25 11 8 11ZM12.2 6.8C11.05 5.65 9.55 5 8 5C6.45 5 4.95 5.65 3.8 6.8C3.4 7.2 2.8 7.2 2.4 6.8C2 6.4 2 5.8 2.4 5.4C3.9 3.9 5.9 3 8 3C10.1 3 12.1 3.9 13.6 5.4C14 5.8 14 6.4 13.6 6.8C13.2 7.2 12.6 7.2 12.2 6.8ZM15 4C13.15 2.15 10.65 1.1 8 1.1C5.35 1.1 2.85 2.15 1 4C0.6 4.4 0 4.4 -0.4 4C-0.8 3.6 -0.8 3 0 2.6C2.1 0.5 5 0 8 0C11 0 13.9 0.5 16 2.6C16.4 3 16.4 3.6 16 4C15.6 4.4 15 4.4 15 4Z"/>
        </svg>
        <svg width="22" height="11" viewBox="0 0 22 11" fill="currentColor">
          <rect x="0" y="0" width="18" height="11" rx="2.5" fill-opacity="0.3"/>
          <rect x="1.5" y="1.5" width="12" height="8" rx="1.5"/>
          <path d="M19.5 3.5V7.5C20.3 7.2 21 6.4 21 5.5C21 4.6 20.3 3.8 19.5 3.5Z"/>
        </svg>
      </div>
    </div>

    <!-- Header Wave Section -->
    <div class="header-wave-section wave-variant-1">
      <div class="header-logo-container">
        <svg class="momas-logo-svg" viewBox="0 0 100 65" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="white"/>
          <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="white"/>
          <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="white"/>
        </svg>
        <span class="momas-logo-text">MOMASPay</span>
      </div>

      <div class="header-titles">
        <h2>My Profile</h2>
        <p>Account Settings & Preferences</p>
      </div>
    </div>

    <!-- Form & Profile Content -->
    <div class="form-content" style="padding-top: 10px; justify-content: flex-start; gap: 16px;">
      
      <!-- User Profile Card -->
      <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 20px; padding: 20px; text-align: center; position: relative;">
        <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 12px;">
          <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80" alt="User Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #00C868; box-shadow: 0 6px 16px rgba(0, 168, 89, 0.2);">
          <button type="button" style="position: absolute; bottom: 0; right: 0; background: #00A859; color: #FFF; border: 2px solid #FFF; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Change Photo" onclick="MomasAlert.success('Photo Updated', 'Your profile image has been updated.')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
          </button>
        </div>
        <h3 style="font-weight: 800; font-size: 1.15rem; color: #0F172A; margin: 0;">James Doe</h3>
        <div style="font-size: 0.8rem; color: #64748B; margin-top: 4px; font-weight: 600;">james.doe@gmail.com</div>
        <div style="margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; color: #047857; font-weight: 700;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          Verified Customer · Meter: 04218849201
        </div>
      </div>

      <!-- Account Settings Form -->
      <form id="user-profile-form" action="{{ route('dashboard') }}" method="GET" style="display: flex; flex-direction: column; gap: 14px;">
        
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
              </svg>
            </span>
            <input type="text" class="input-field" value="James Doe" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </span>
            <input type="email" class="input-field" value="james.doe@gmail.com" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </span>
            <input type="tel" class="input-field" value="+234 803 123 4567" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Default Estate Address</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11"/>
              </svg>
            </span>
            <input type="text" class="input-field" value="Flat 4B, Block 12, EVE Estate, Ikeja">
          </div>
        </div>



        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 14px;">
          <button type="submit" class="btn-primary">SAVE CHANGES</button>
          <button type="button" style="background: #FEE2E2; color: #DC2626; border: none; padding: 14px; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.88rem;" onclick="confirmLogout()">
            LOGOUT ACCOUNT
          </button>
        </div>
      </form>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="bottom-nav-bar">
      <a href="{{ route('dashboard') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
      </a>
      <a href="{{ route('reprint-token') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
      </a>
      <a href="{{ route('profile') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </a>
      <a href="{{ route('support') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
    </div>

  </div>

  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('user-profile-form');
      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          if (typeof MomasAlert !== 'undefined') {
            MomasAlert.success(
              'Profile Saved!',
              'Your account details have been updated successfully.',
              () => {
                window.location.href = 'dashboard.html';
              }
            );
          } else {
            alert('Profile Saved!');
            window.location.href = 'dashboard.html';
          }
        });
      }
    });

    function confirmLogout() {
      if (typeof MomasAlert !== 'undefined') {
        MomasAlert.danger(
          'Logout from MomasPay?',
          'Are you sure you want to sign out of your account?',
          'Yes, Logout',
          () => {
            window.location.href = 'login.html';
          }
        );
      } else {
        if (confirm('Are you sure you want to logout?')) {
          window.location.href = 'login.html';
        }
      }
    }
  </script>
@endsection
