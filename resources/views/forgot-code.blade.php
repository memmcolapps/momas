@extends('layouts.app')

@section('title', 'Forgot Password - Code Validation - MomasPay')
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

    <!-- Header Section -->
    <div class="header-wave-section">
      <div class="header-nav">
        <a href="{{ route('forgot-password') }}" class="back-btn" title="Back">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
        </a>
        <div class="header-logo-container align-right" style="margin: 0;">
          <svg class="momas-logo-svg" style="width: 54px; height: 36px;" viewBox="0 0 100 65" fill="none">
            <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="white"/>
            <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="white"/>
            <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="white"/>
          </svg>
          <span class="momas-logo-text" style="font-size: 0.65rem;">MOMASPay</span>
        </div>
      </div>

      <div class="header-titles">
        <h2>Code Validation</h2>
      </div>
    </div>

    <!-- Form Section -->
    <div class="form-content">
      <div style="margin-bottom: 4px;">
        <p class="email-greeting" style="font-size: 0.95rem; margin-bottom: 2px;">Hi Jimmy,</p>
        <p class="email-notice-text">
          4 Digit code has been sent to <span>toluadejimi@gmail.com</span> check your inbox or spam folder
        </p>
      </div>

      <form action="{{ route('reset-password') }}" method="GET">
        <div class="form-group" style="margin-top: 10px;">
          <label class="form-label">Enter valid code</label>
          <div class="otp-container">
            <input type="text" class="otp-box" maxlength="1" value="4">
            <input type="text" class="otp-box" maxlength="1" value="5">
            <input type="text" class="otp-box" maxlength="1" value="6">
            <input type="text" class="otp-box" maxlength="1" value="7">
          </div>
        </div>

        <div class="otp-resend-row">
          <span class="timer-text" id="otp-timer">02:05</span>
          <a href="#" class="resend-link">Send code again</a>
        </div>

        <div class="btn-row" style="margin-top: 20px;">
          <a href="{{ route('reset-password') }}" class="btn-primary">CONTINUE</a>
        </div>
      </form>
    </div>
  </div>
@endsection
