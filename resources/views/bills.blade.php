@extends('layouts.app')

@section('title', 'Bills Payment - MomasPay Plus')
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
        <a href="{{ route('dashboard') }}" class="back-btn" aria-label="Go back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
        </a>
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Bills Payment</h3>
      </div>

      <!-- Bills Category Cards List -->
      <div class="support-cards-list" style="margin-top: 10px;">
        
        <!-- Option 1: Airtime -->
        <a href="{{ route('airtime') }}" class="support-card-item">
          <div class="support-icon-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
              <line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
          </div>
          <div class="support-card-text">
            <h4>Airtime</h4>
            <p>Buy airtime for all Networks</p>
          </div>
        </a>

        <!-- Option 2: Data Bundle -->
        <a href="{{ route('airtime') }}" class="support-card-item">
          <div class="support-icon-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2"></rect>
              <path d="M6 8h.01M10 8h.01M14 8h.01M18 8h.01M6 12h.01M10 12h.01M14 12h.01M18 12h.01"></path>
            </svg>
          </div>
          <div class="support-card-text">
            <h4>Data Bundle</h4>
            <p>Buy data for all Network</p>
          </div>
        </a>

      </div>

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

  </main>
@endsection
