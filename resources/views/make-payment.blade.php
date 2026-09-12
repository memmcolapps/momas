@extends('layouts.app')

@section('title', 'Make Payment for MOMAS - MomasPay')
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

    <!-- Solid Green Header Bar -->
    <div style="background: #00A859; height: 90px; padding: 44px 20px 0; position: relative;">
      <!-- Floating Header Card -->
      <div class="make-payment-header-card">
        <a href="{{ route('dashboard') }}" class="back-btn" style="width: 36px; height: 36px; box-shadow: none; background: #F1F5F9;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
        </a>
        <h3>Make Payment for MOMAS</h3>
      </div>
    </div>

    <!-- Page Body Content -->
    <div class="form-content" style="padding-top: 68px; justify-content: flex-start;">
      <p class="email-notice-text">Make payment on your momas meter easily in few steps</p>

      <!-- Input 1: Meter Number with Verify button -->
      <div class="form-group" style="margin-top: 10px;">
        <label class="form-label">Meter Number</label>
        <div style="display: flex; gap: 10px; align-items: center;">
          <div class="input-container" style="flex: 1;">
            <span class="input-icon-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="4" y="4" width="16" height="16" rx="2"/>
                <path d="M12 9v4l2.5 2.5"/>
              </svg>
            </span>
            <input type="text" class="input-field" placeholder="545533662672" value="545533662672">
          </div>
          <button type="button" class="btn-verify">VERIFY</button>
        </div>
      </div>

      <!-- Verified User Name Badge -->
      <div class="verified-user-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <span>Adejimi Tolulope Adewale</span>
      </div>

      <!-- Input 2: Amount -->
      <div class="form-group" style="margin-top: 10px;">
        <label class="form-label">Amount (NGN)</label>
        <div class="input-container">
          <input type="text" class="input-field" style="padding-left: 16px;" placeholder="2,000" value="2,000">
        </div>
        <span style="font-size: 0.75rem; color: #94A3B8; margin-top: 4px;">Min 1,000 | Max 1,000,000</span>
      </div>

      <!-- CONTINUE Button (Triggers Gateway Modal) -->
      <div class="btn-row" style="margin-top: 30px;">
        <button id="open-gateway-btn" type="button" class="btn-primary">CONTINUE</button>
      </div>
    </div>

    <!-- Fixed Bottom Mobile Navigation Bar -->
    <div class="bottom-nav-bar">
      <a href="{{ route('dashboard') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2L2 9v12a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V9L12 2z"/>
        </svg>
      </a>
      <a href="#" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
      </a>
      <a href="#" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
    </div>

    <!-- PAYMENT GATEWAY BOTTOM SHEET MODAL -->
    <div class="modal-overlay" id="gateway-modal">
      <div class="bottom-sheet-modal">
        <div class="sheet-handle-bar"></div>
        <span class="sheet-title">Choose preferred payment gateway</span>

        <div class="sheet-amount-row">
          <div>
            <span class="to-pay-label">To pay</span>
            <div class="to-pay-val">NGN2,000</div>
          </div>
          <div style="text-align: right;">
            <span style="font-size: 0.68rem; color: #94A3B8; font-weight: 600;">Services</span>
            <div style="display: flex; align-items: center; gap: 4px;">
              <svg width="24" height="16" viewBox="0 0 100 65" fill="none">
                <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#00A859"/>
                <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#00A859"/>
                <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#00A859"/>
              </svg>
              <span style="font-weight: 800; font-size: 0.85rem; color: #00A859;">MOMAS</span>
            </div>
          </div>
        </div>

        <div class="gateway-options-list">
          
          <!-- Option 1: Paystack -->
          <a href="{{ route('payment-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box paystack">P</div>
              <span>Pay with Paystack</span>
            </div>
          </a>

          <!-- Option 2: Flutterwave -->
          <a href="{{ route('payment-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box flutterwave">F</div>
              <span>Pay with Flutterwave</span>
            </div>
          </a>

          <!-- Option 3: Wallet -->
          <a href="{{ route('payment-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box wallet">👛</div>
              <span>Pay with wallet</span>
            </div>
            <span class="gateway-balance-sub">NGN 100,000</span>
          </a>

        </div>
      </div>
    </div>

  </div>
@endsection
