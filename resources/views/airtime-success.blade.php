@extends('layouts.app')

@section('title', 'Airtime Payment Successful - MomasPay Plus')
@section('body_class', '')

@section('content')
<!-- Top Navigation Bar -->
  <!-- Mobile Screen Container (Mint Success Background) -->
  <main class="page-container success-bg" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px;">

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

    <!-- Green Success Check Ring Icon -->
    <div class="success-icon-wrap" style="margin-bottom: 20px; width: 90px; height: 90px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
        <polyline points="20 6 9 17 4 12"/>
      </svg>
    </div>

    <!-- Title -->
    <h2 style="color: #00A859; font-weight: 800; font-size: 1.35rem; margin-bottom: 20px; text-align: center;">Payment Successful</h2>

    <!-- Zigzag Serrated Paper Receipt Card -->
    <div class="receipt-wrapper">
      <div class="receipt-title">Purchase Details</div>

      <!-- Order ID -->
      <div class="receipt-row">
        <span class="receipt-row-label">OrderId:</span>
        <div class="receipt-copy-box">
          <span class="receipt-row-val">24063372JM</span>
          <button type="button" class="copy-badge-icon" aria-label="Copy Order ID">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="9" y="9" width="13" height="13" rx="2"></rect>
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
          </button>
        </div>
      </div>

      <div class="receipt-divider"></div>

      <!-- Phone -->
      <div class="receipt-row">
        <span class="receipt-row-label">Phone:</span>
        <span class="receipt-row-val">08195959753</span>
      </div>

      <div class="receipt-divider"></div>

      <!-- Service -->
      <div class="receipt-row">
        <span class="receipt-row-label">Service:</span>
        <span class="receipt-row-val">MTN NIGERIA AIRTIME</span>
      </div>

      <div class="receipt-divider"></div>

      <!-- Amount -->
      <div class="receipt-row">
        <span class="receipt-row-label">Amount:</span>
        <span class="receipt-row-val" style="font-weight: 800; color: #0F172A;">NGN 1,000</span>
      </div>

      <div class="receipt-footer-text">Thank you for choosing momas pay</div>
    </div>

    <!-- Square Action Buttons (Home & Share) -->
    <div class="action-buttons-row" style="margin-top: 15px;">
      <a href="{{ route('dashboard') }}" class="square-action-btn" aria-label="Home">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
      </a>
      <button type="button" class="square-action-btn" onclick="if(navigator.share){navigator.share({title:'Airtime Receipt',text:'MTN Airtime NGN1,000 sent to 08195959753'})}else{alert('Receipt details copied!')}" aria-label="Share">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="18" cy="5" r="3"/>
          <circle cx="6" cy="12" r="3"/>
          <circle cx="18" cy="19" r="3"/>
          <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
          <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
        </svg>
      </button>
    </div>

    <!-- Bottom Dark Gradient Pill Button -->
    <a href="{{ route('dashboard') }}" class="dark-pill-btn" style="margin-top: 10px;">LEARN HOW TO ACTIVATE TOKEN</a>

  </main>
@endsection
