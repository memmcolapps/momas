@extends('layouts.app')

@section('title', 'Verification Email Received - MomasPay')
@section('body_class', '')

@section('content')
<!-- Mobile Screen Container -->
  <div class="page-container">
    
    <div class="email-card-screen">
      <div class="email-card-logo">
        <svg class="momas-logo-svg" viewBox="0 0 100 65" fill="none">
          <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#00A859"/>
          <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#00A859"/>
          <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#00A859"/>
        </svg>
        <span class="momas-text-green">MOMASPay</span>
      </div>

      <div class="email-body">
        <p class="email-greeting">Hi Jimmy,</p>
        <p class="email-message">Your verification code is</p>
        <p class="email-otp-code">4567</p>
        <p class="email-warning">Do not share code with anyone</p>
        <p class="email-disclaimer">Kindly disregard the email if your didn't make the request</p>
      </div>

      <div style="margin-top: auto; padding-top: 30px;">
        <a href="{{ route('code-validation') }}" class="btn-primary" style="padding: 0 24px; height: 44px; font-size: 0.85rem;">Enter Code in App &rarr;</a>
      </div>
    </div>
  </div>
@endsection
