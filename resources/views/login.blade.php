@extends('layouts.app')

@section('title', 'Login - MomasPay')
@section('body_class', '')

@section('content')
<!-- Mobile Screen Container -->
  <div class="page-container">
    
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
        <h2>Welcome Back</h2>
        <p>Login</p>
      </div>
    </div>

    <!-- Form Section -->
    <div class="form-content">
      <form action="{{ route('hub') }}" method="GET">
        <div class="form-group">
          <label class="form-label">Email / Meter No</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </span>
            <input type="text" class="input-field" placeholder="Login@rmail.com" value="Login@rmail.com">
          </div>
        </div>

        <div class="form-group" style="margin-top: 14px;">
          <label class="form-label">Password</label>
          <div class="input-container">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
            <input type="password" class="input-field" placeholder="*******" value="password123">
            <span class="input-icon-right toggle-password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </span>
          </div>
        </div>

        <div class="forgot-password-wrap" style="margin-top: 8px;">
          <a href="{{ route('forgot-password') }}" class="forgot-password-link">Forgot Password</a>
        </div>

        <div class="btn-row" style="margin-top: 18px;">
          <a href="{{ route('dashboard') }}" class="btn-primary">LOGIN</a>
          <button type="button" class="biometric-btn" title="Fingerprint Login">
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

      <div class="form-footer">
        New on MOMAS PAY? <a href="{{ route('email-verification') }}">Register Here</a>
      </div>
    </div>
  </div>
@endsection
