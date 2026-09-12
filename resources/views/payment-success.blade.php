@extends('layouts.app')

@section('title', 'Payment Successful - MomasPay')
@section('body_class', '')

@section('content')
<!-- Mobile Screen Container -->
  <div class="page-container success-bg" style="justify-content: space-between; padding-bottom: 25px;">
    
    <!-- iOS Status Bar -->
    <div class="status-bar dark-text">
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

    <!-- Main Content Area -->
    <div style="padding-top: 55px; text-align: center;">
      
      <!-- Green Checkmark Badge -->
      <div class="success-icon-wrap" style="width: 80px; height: 80px; margin: 0 auto 15px; border-width: 5px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 36px; height: 36px;">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>

      <h2 style="font-size: 1.35rem; font-weight: 800; color: #00A859; margin-bottom: 20px;">Payment Successful</h2>

      <!-- Serrated White Paper Receipt -->
      <div class="receipt-wrapper">
        <h3 class="receipt-title">Purchase Details</h3>

        <div class="receipt-row">
          <span class="receipt-row-label">OrderId:</span>
          <div class="receipt-copy-box">
            <span class="receipt-row-val">24063372JM</span>
            <span class="copy-badge-icon" title="Copy Order ID">📋</span>
          </div>
        </div>

        <div class="receipt-divider"></div>

        <div class="receipt-row">
          <span class="receipt-row-label">Name:</span>
          <span class="receipt-row-val">Adejimi Tolulope Adewale</span>
        </div>

        <div class="receipt-row">
          <span class="receipt-row-label">Address:</span>
          <span class="receipt-row-val">4, Bode Thomas, Surulere, lagos</span>
        </div>

        <div class="receipt-divider"></div>

        <div class="receipt-row">
          <span class="receipt-row-label">Service:</span>
          <span class="receipt-row-val">IKEDC</span>
        </div>

        <div class="receipt-row">
          <span class="receipt-row-label">Token:</span>
          <div class="receipt-copy-box">
            <span class="receipt-row-val" style="font-size: 0.78rem; word-break: break-all;">45337747334858586683</span>
            <span class="copy-badge-icon" title="Copy Token">📋</span>
          </div>
        </div>

        <div class="receipt-row">
          <span class="receipt-row-label">Amount:</span>
          <span class="receipt-row-val" style="font-size: 1.05rem; font-weight: 800;">NGN 2,000</span>
        </div>

        <p class="receipt-footer-text">Thank you for choosing momas pay</p>
      </div>

      <!-- Action Buttons Row -->
      <div class="action-buttons-row">
        <a href="{{ route('dashboard') }}" class="square-action-btn" title="Home">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L2 9v12a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V9L12 2z"/>
          </svg>
        </a>
        <button type="button" class="square-action-btn" title="Share Receipt">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <circle cx="18" cy="5" r="3"/>
            <circle cx="6" cy="12" r="3"/>
            <circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
          </svg>
        </button>
      </div>

      <!-- Dark Pill Button -->
      <a href="#" class="dark-pill-btn">LEARN HOW TO ACTIVATE TOKEN</a>
    </div>

  </div>
@endsection
