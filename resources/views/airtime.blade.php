@extends('layouts.app')

@section('title', 'Buy Airtime - MomasPay Plus')
@section('body_class', '')

@section('content')
<!-- Top Navigation Bar -->
  <!-- Mobile Screen Container -->
  <main class="page-container" style="padding-bottom: 75px;">

    <!-- Status Bar -->
    <div class="status-bar dark-text">
      <span>9:41</span>
      <div style="display: flex; gap: 5px; align-items: center;">
        <svg width="14" height="12" viewBox="0 0 16 12" fill="currentColor">
          <path d="M1 4.5A8.5 8.5 0 0 1 15 4.5M3.5 7.5A5.5 5.5 0 0 1 12.5 7.5M6 10.5A2.5 2.5 0 0 1 10 10.5"/>
        </svg>
        <svg width="15" height="11" viewBox="0 0 18 12" fill="currentColor">
          <path d="M1 10h2V8H1v2zm4 0h2V6H5v4zm4 0h2V4H9v6zm4 0h2V2h-2v8zm4 0h2V0h-2v10z"/>
        </svg>
        <svg width="20" height="10" viewBox="0 0 24 12" fill="currentColor">
          <rect x="1" y="1" width="18" height="10" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
          <rect x="3" y="3" width="14" height="6" rx="1.5"/>
          <path d="M21 4v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
    </div>

    <!-- Main Body Content -->
    <div class="form-content" style="padding-top: 20px; gap: 20px;">
      
      <!-- Top Floating Header Card -->
      <div class="make-payment-header-card" style="margin-top: 0;">
        <a href="{{ route('bills') }}" class="back-btn" aria-label="Go back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
        </a>
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Airtime</h3>
      </div>

      <!-- Airtime Purchase Form -->
      <form style="display: flex; flex-direction: column; gap: 20px; margin-top: 10px;">
        
        <!-- Select Network Section -->
        <div class="form-group">
          <label class="form-label" style="font-weight: 700; color: #1E293B; font-size: 0.88rem;">Select Network</label>
          <div class="network-selector-grid">
            
            <!-- MTN Badge -->
            <div class="network-badge-box selected" data-network="MTN">
              <span class="network-logo-badge-icon mtn">MTN</span>
            </div>

            <!-- Glo Badge -->
            <div class="network-badge-box" data-network="Glo">
              <span class="network-logo-badge-icon glo">glo</span>
            </div>

            <!-- Airtel Badge -->
            <div class="network-badge-box" data-network="Airtel">
              <span class="network-logo-badge-icon airtel">airtel</span>
            </div>

            <!-- 9mobile Badge -->
            <div class="network-badge-box" data-network="9mobile">
              <span class="network-logo-badge-icon mobile9">9mobile</span>
            </div>

          </div>
        </div>

        <!-- Phone Field -->
        <div class="form-group">
          <label class="form-label" style="font-weight: 700; color: #1E293B;">Phone</label>
          <div class="input-container">
            <input type="tel" class="input-field" value="08195959753" style="padding-left: 16px; padding-right: 48px; border-color: #00A859; height: 52px; border-radius: 16px; font-weight: 700; color: #1E293B;">
            <button type="button" class="input-icon-right" style="background: none; border: none; color: #00A859;" aria-label="Choose Contact">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2"></rect>
                <path d="M12 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
                <path d="M8 15a4 4 0 0 1 8 0"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Amount Field -->
        <div class="form-group">
          <label class="form-label" style="font-weight: 700; color: #1E293B;">Amount</label>
          <div class="input-container">
            <input type="text" class="input-field" value="1,000" style="padding-left: 16px; border-color: #00A859; height: 52px; border-radius: 16px; font-weight: 700; color: #1E293B;">
          </div>
        </div>

        <!-- Action Button -->
        <button type="button" id="open-gateway-btn" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 10px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">BUY NOW</button>

      </form>

    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav-bar">
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
      <a href="{{ route('support') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
  </nav>

    <!-- Payment Gateway Bottom Sheet Modal -->
    <div id="gateway-modal" class="modal-overlay">
      <div class="bottom-sheet-modal">
        <div class="sheet-handle-bar"></div>
        <div class="sheet-title">Choose preferred payment gateway</div>
        
        <div class="sheet-amount-row">
          <div>
            <div class="to-pay-label">To pay</div>
            <div class="to-pay-val">NGN1,000</div>
          </div>
          <div style="text-align: right;">
            <div class="to-pay-label">Services</div>
            <div class="to-pay-val" id="gateway-airtime-service" style="font-size: 1rem; color: #1E293B;">MTN AIRTIME</div>
          </div>
        </div>

        <div class="gateway-options-list">
          <!-- Option 1: Paystack -->
          <a href="{{ route('airtime-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box paystack">P</div>
              <span>Pay with Paystack</span>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>

          <!-- Option 2: Flutterwave -->
          <a href="{{ route('airtime-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box flutterwave">F</div>
              <span>Pay with Flutterwave</span>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>

          <!-- Option 3: Wallet -->
          <a href="{{ route('airtime-success') }}" class="gateway-option-item">
            <div class="gateway-left-info">
              <div class="gateway-logo-box wallet">👛</div>
              <div>
                <div>Pay with wallet</div>
                <div class="gateway-balance-sub">NGN 100,000</div>
              </div>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>
        </div>
      </div>
    </div>

  </main>
@endsection
