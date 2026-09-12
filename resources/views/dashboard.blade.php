@extends('layouts.app')

@section('title', 'Dashboard - MomasPay')
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

    <!-- Header Section with Balances -->
    <div class="dash-header-section">
      <div class="dash-top-bar">
        <h2 class="user-greeting">Hi Jimmy,</h2>
        <div class="top-bar-right">
          <a href="{{ route('support') }}" class="bell-btn" title="Notifications & Support" aria-label="Support">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </a>
          <a href="{{ route('profile') }}" title="User Profile">
            <svg class="avatar-img" viewBox="0 0 100 100" fill="#E2E8F0">
              <circle cx="50" cy="50" r="50" fill="#E2E8F0"/>
              <circle cx="50" cy="40" r="20" fill="#64748B"/>
              <path d="M20,90 C20,70 35,65 50,65 C65,65 80,70 80,90 Z" fill="#64748B"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- Wallet & Units Balances with Working Eye Toggles -->
      <div class="balances-grid">
        <div class="balance-item">
          <span class="balance-label">Main Wallet</span>
          <div class="balance-value-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="5" width="20" height="14" rx="2"/>
              <path d="M16 12h.01"/>
            </svg>
            <span class="balance-text" data-original="100,000.00">100,000.00</span>
            <button type="button" class="toggle-balance-btn" title="Toggle Visibility">
              <svg class="eye-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="balance-item">
          <span class="balance-label">Available Units</span>
          <div class="balance-value-row small-text">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
            <span class="balance-text" data-original="19.300908...">19.300908...</span>
            <button type="button" class="toggle-balance-btn" title="Toggle Visibility">
              <svg class="eye-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Floating Quick Action Pill Row -->
      <div class="floating-action-bar">
        <a href="{{ route('make-payment') }}" class="action-pill-btn">
          <span class="action-pill-icon">💳</span> Buy Units
        </a>
        <a href="{{ route('make-payment') }}" class="action-pill-btn">
          <span class="action-pill-icon">👛</span> Fund Wallet
        </a>
        <a href="{{ route('generate-token') }}" class="action-pill-btn">
          <span class="action-pill-icon">🏢</span> Services
        </a>
      </div>
    </div>

    <!-- Dashboard Body Area -->
    <div class="dash-body">
      
      <!-- Interactive Promo Banner Carousel Slider -->
      <div class="promo-carousel-wrapper">
        <div class="promo-slider-container" id="promo-slider">
          
          <!-- Slide 1 -->
          <div class="promo-banner-card">
            <div>
              <h4>Payment <span>Made Easy</span></h4>
              <p>Buy instant unit on MOMASPAY</p>
            </div>
            <a href="{{ route('make-payment') }}" class="promo-buy-btn">Buy Now</a>
            <svg class="promo-logo-watermark" width="40" height="26" viewBox="0 0 100 65" fill="none">
              <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="rgba(255,255,255,0.4)"/>
              <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="rgba(255,255,255,0.4)"/>
              <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="rgba(255,255,255,0.4)"/>
            </svg>
          </div>

          <!-- Slide 2 -->
          <div class="promo-banner-card slide-2">
            <div>
              <h4>24/7 Meter <span>Recharge</span></h4>
              <p>Recharge electricity anytime, anywhere</p>
            </div>
            <a href="{{ route('pay-other-meter') }}" class="promo-buy-btn">Recharge</a>
            <svg class="promo-logo-watermark" width="40" height="26" viewBox="0 0 100 65" fill="none">
              <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="rgba(255,255,255,0.4)"/>
              <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="rgba(255,255,255,0.4)"/>
              <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="rgba(255,255,255,0.4)"/>
            </svg>
          </div>

          <!-- Slide 3 -->
          <div class="promo-banner-card slide-3">
            <div>
              <h4>Estate <span>Bill Services</span></h4>
              <p>Pay estate bills and maintenance easily</p>
            </div>
            <a href="{{ route('generate-token') }}" class="promo-buy-btn">Explore</a>
            <svg class="promo-logo-watermark" width="40" height="26" viewBox="0 0 100 65" fill="none">
              <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="rgba(255,255,255,0.4)"/>
              <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="rgba(255,255,255,0.4)"/>
              <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="rgba(255,255,255,0.4)"/>
            </svg>
          </div>

        </div>

        <!-- Slider Dots Indicators -->
        <div class="slider-dots" id="promo-dots">
          <span class="dot active" data-slide="0"></span>
          <span class="dot" data-slide="1"></span>
          <span class="dot" data-slide="2"></span>
        </div>
      </div>

      <!-- Services 3x3 Grid Section -->
      <span class="services-section-title">What will you like to do?</span>

      <div class="services-grid-wrapper">
        <a href="{{ route('make-payment') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="1" y="4" width="22" height="16" rx="2"/>
              <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
          </div>
          <span class="service-title">Make Payment</span>
          <span class="service-sub">Buy extra unit for your momas meter</span>
        </a>

        <a href="{{ route('pay-other-meter') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="6" width="20" height="12" rx="2"/>
              <path d="M12 12a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
            </svg>
          </div>
          <span class="service-title">Pay Other Meter</span>
          <span class="service-sub">Buy unit for other meters</span>
        </a>

        <a href="{{ route('reprint-token') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 6 2 18 2 18 9"/>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
              <rect x="6" y="14" width="12" height="8"/>
            </svg>
          </div>
          <span class="service-title">Reprint Token</span>
          <span class="service-sub">Reprint your purchased token</span>
        </a>

        <a href="{{ route('generate-token') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 2l-2 2m-2-2l2 2m7 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
          </div>
          <span class="service-title">Access Token</span>
          <span class="service-sub">Generate and manage security token</span>
        </a>

        <a href="{{ route('service') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2"/>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
          </div>
          <span class="service-title">Services</span>
          <span class="service-sub">Request for any services in your estate</span>
        </a>


        <a href="{{ route('bills') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="5" y="2" width="14" height="20" rx="2"/>
              <line x1="12" y1="18" x2="12.01" y2="18"/>
            </svg>
          </div>
          <span class="service-title">Bill Payment</span>
          <span class="service-sub">Manage and add beneficiary to your account</span>
        </a>

        <a href="{{ route('support') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
              <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
            </svg>
          </div>
          <span class="service-title">Support</span>
          <span class="service-sub">Contact our 24/7 support</span>
        </a>

        <a href="{{ route('make-payment') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="M16 12h4"/>
            </svg>
          </div>
          <span class="service-title">Top up wallet</span>
          <span class="service-sub">Fund your wallet easily</span>
        </a>

        <a href="{{ route('analytics') }}" class="service-card">
          <div class="service-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
              <path d="M22 12A10 10 0 0 0 12 2v10z"/>
            </svg>
          </div>
          <span class="service-title">Analytics</span>
          <span class="service-sub">Buy Airtime and Data for all network</span>
        </a>


      </div>
    </div>

    <!-- Fixed Bottom Navigation Bar -->
    <div class="bottom-nav-bar">
      <a href="{{ route('dashboard') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2L2 9v12a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V9L12 2z"/>
        </svg>
      </a>
      <a href="{{ route('reprint-token') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
      </a>
      <a href="{{ route('profile') }}" class="bottom-nav-item">
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
@endsection
