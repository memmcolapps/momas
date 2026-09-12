@extends('layouts.app')

@section('title', 'Admin Executive Login - MomasPay Enterprise')

@section('content')
<!-- 100% Fullscreen Edge-to-Edge 2-Column Split Desktop Layout -->
  <div class="admin-login-fullscreen">

    <!-- LEFT SIDE (50% Fullscreen Column): Full-Cover Image & Color Blur Overlay -->
    <div class="admin-hero-panel">
      
      <!-- Full Cover Background Image -->
      <img src="{{ asset('img/admin-hero-banner.jpg') }}" alt="Smart Meter Grid Vector" class="hero-bg-image">
      
      <!-- Color & Backdrop Blur Overlay -->
      <div class="hero-bg-overlay"></div>

      <!-- Foreground Content -->
      <div class="hero-content-inner">
        <div>
          <div class="hero-brand-top">
            <div class="brand-logo-group">
              <svg class="hero-logo-svg" viewBox="0 0 100 65" fill="none">
                <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="white"/>
                <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="white"/>
                <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="white"/>
              </svg>
              <span class="hero-brand-title">MomasPay</span>
            </div>
            <span class="enterprise-tag-pill">Executive Console</span>
          </div>

          <div class="hero-main-headline">
            Smart Utility Vending & Executive Infrastructure Platform
          </div>
          <p class="hero-subtext">
            Enterprise control console for STS 20-digit electricity token vending, estate tariffs, transformer feeders, and real-time audit intelligence.
          </p>

          <div class="hero-feature-list">
            <div class="hero-feature-pill">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
              <span>Instant STS 20-Digit Vending Gateway</span>
            </div>
            <div class="hero-feature-pill">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span>256-Bit SSL Encrypted Executive Access</span>
            </div>
            <div class="hero-feature-pill">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11"/></svg>
              <span>Multi-Estate & Transformer Feeder Grid</span>
            </div>
          </div>
        </div>

        <div class="hero-footer-note">
          © 2026 MomasPay Executive Systems · All Rights Reserved
        </div>
      </div>

    </div>

    <!-- RIGHT SIDE (50% Fullscreen Column): Credentials Form Panel -->
    <div class="admin-form-panel">
      <div class="form-content-wrap">
        
        <div class="form-header-box">
          <div class="form-header-title">Welcome Back</div>
          <p class="form-header-subtitle">Sign in to access your Executive Admin Console</p>
        </div>

        <div class="security-badge-banner">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>256-Bit SSL Encrypted Admin Portal</span>
        </div>

        <form id="admin-login-form" action="{{ route('admin.dashboard') }}" method="GET">
          
          <!-- Admin Email / Username -->
          <div class="form-group">
            <label class="form-label">Admin Email / Username</label>
            <div class="input-container">
              <span class="input-icon-left">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
              </span>
              <input type="email" class="input-field" placeholder="admin@momaspay.com" value="admin@momaspay.com" required>
            </div>
          </div>

          <!-- Password -->
          <div class="form-group" style="margin-top: 18px;">
            <label class="form-label">Password</label>
            <div class="input-container">
              <span class="input-icon-left">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input type="password" class="input-field" placeholder="*******" value="momasadmin2026" required>
              <span class="input-icon-right toggle-password">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </span>
            </div>
          </div>

          <!-- Access Level / System Role Selector -->
          <div class="form-group" style="margin-top: 18px;">
            <label class="form-label">Access Tier / System Role</label>
            <div class="input-container">
              <span class="input-icon-left">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
              </span>
              <select class="input-field" style="cursor: pointer; font-weight: 700; color: #00A859;">
                <option value="Super Admin">Super Administrator (Level 1 Root)</option>
                <option value="Finance Officer">Finance & Tariff Manager</option>
                <option value="Support Agent">Support Escalation Lead</option>
              </select>
            </div>
          </div>

          <!-- Forgot Credentials Link -->
          <div class="forgot-password-wrap" style="margin-top: 12px;">
            <a href="{{ route('admin.profile') }}" class="forgot-password-link">Forgot Credentials?</a>
          </div>

          <!-- Action Button Row (LOGIN + Passkey Biometrics) -->
          <div class="btn-row" style="margin-top: 24px;">
            <button type="submit" class="btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 8px; border: none; cursor: pointer; height: 50px;">
              <span>SIGN IN TO CONSOLE</span>
              &rarr;
            </button>
            <button type="button" class="biometric-btn" style="height: 50px; width: 50px;" title="Passkey / TouchID Login" onclick="triggerBiometricAuth()">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/>
                <path d="M14 13.12c0 2.38-.4 4.88-1.5 7.88"/>
                <path d="M2 12a10 10 0 0 1 18-6"/>
                <path d="M20 12a10 10 0 0 1-4.22 8.12"/>
                <path d="M18 12a6 6 0 0 0-10.4-4.22"/>
                <path d="M6 12a6 6 0 0 0 1.82 4.36"/>
                <path d="M14 12a2 2 0 0 0-3.8-1"/>
              </svg>
            </button>
          </div>

        </form>

        <div class="form-footer" style="margin-top: 32px; text-align: center;">
          Executive Admin System · <a href="{{ route('hub') }}">Showcase Hub</a>
        </div>

      </div>
    </div>

  </div>

  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('admin-login-form');
      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          if (typeof MomasAlert !== 'undefined') {
            MomasAlert.success(
              'Welcome Back, Administrator!',
              'Authenticating executive session...',
              () => {
                window.location.href = 'admin-dashboard.html';
              }
            );
          } else {
            alert('Welcome Back, Administrator!');
            window.location.href = 'admin-dashboard.html';
          }
        });
      }
    });

    function triggerBiometricAuth() {
      if (typeof MomasAlert !== 'undefined') {
        MomasAlert.success(
          'Biometric Passkey Verified!',
          'Executive TouchID / FaceID confirmed. Entering Admin Console...',
          () => {
            window.location.href = 'admin-dashboard.html';
          }
        );
      } else {
        alert('Passkey Verified!');
        window.location.href = 'admin-dashboard.html';
      }
    }
  </script>
@endsection
