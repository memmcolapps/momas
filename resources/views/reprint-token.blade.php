@extends('layouts.app')

@section('title', 'Reprint Token - MomasPay Plus')
@section('body_class', '')

@section('content')
<!-- Top Navigation for Direct Access -->
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
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Reprint Token</h3>
      </div>

      <!-- Subtitle -->
      <p style="font-size: 0.82rem; color: #64748B; font-weight: 500; margin-top: 10px;">Easily reprint all purchased token</p>

      <!-- Search & Filter Row -->
      <div class="token-search-row">
        <div class="input-container" style="flex: 1;">
          <span class="input-icon-left" style="color: #00A859;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
          <input type="text" id="token-search" class="input-field" placeholder="" style="padding-left: 48px; border-color: #00A859; height: 50px; border-radius: 14px;">
        </div>
        <button type="button" id="open-filter-modal" class="token-filter-btn" style="border-color: #00A859; border-radius: 14px; width: 50px; height: 50px;" aria-label="Filter tokens">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="6" x2="20" y2="6"></line>
            <line x1="7" y1="12" x2="17" y2="12"></line>
            <line x1="10" y1="18" x2="14" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Purchased Token History List -->
      <div class="token-history-list">
        
        <!-- Token Item 1 -->
        <a href="{{ route('payment-success') }}" class="token-card-item">
          <div class="token-card-left">
            <div class="token-icon-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="3"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="12" y2="14"></line>
              </svg>
            </div>
            <div>
              <div class="token-card-title">IKEDC</div>
              <div class="token-card-sub">45446644858574747474573</div>
            </div>
          </div>
          <div class="token-card-right">
            <div class="token-card-time">Yesterday 12:00pm</div>
            <div class="token-card-amount">NGN 2,000</div>
          </div>
        </a>

        <!-- Token Item 2 -->
        <a href="{{ route('payment-success') }}" class="token-card-item">
          <div class="token-card-left">
            <div class="token-icon-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="3"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="12" y2="14"></line>
              </svg>
            </div>
            <div>
              <div class="token-card-title">IKEDC</div>
              <div class="token-card-sub">45446644858574747474573</div>
            </div>
          </div>
          <div class="token-card-right">
            <div class="token-card-time">Yesterday 12:00pm</div>
            <div class="token-card-amount">NGN 2,000</div>
          </div>
        </a>

        <!-- Token Item 3 -->
        <a href="{{ route('payment-success') }}" class="token-card-item">
          <div class="token-card-left">
            <div class="token-icon-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="3"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="12" y2="14"></line>
              </svg>
            </div>
            <div>
              <div class="token-card-title">IKEDC</div>
              <div class="token-card-sub">45446644858574747474573</div>
            </div>
          </div>
          <div class="token-card-right">
            <div class="token-card-time">Yesterday 12:00pm</div>
            <div class="token-card-amount">NGN 2,000</div>
          </div>
        </a>

        <!-- Token Item 4 -->
        <a href="{{ route('payment-success') }}" class="token-card-item">
          <div class="token-card-left">
            <div class="token-icon-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="3"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="12" y2="14"></line>
              </svg>
            </div>
            <div>
              <div class="token-card-title">IKEDC</div>
              <div class="token-card-sub">45446644858574747474573</div>
            </div>
          </div>
          <div class="token-card-right">
            <div class="token-card-time">Yesterday 12:00pm</div>
            <div class="token-card-amount">NGN 2,000</div>
          </div>
        </a>

        <!-- Token Item 5 -->
        <a href="{{ route('payment-success') }}" class="token-card-item">
          <div class="token-card-left">
            <div class="token-icon-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="3"></rect>
                <line x1="8" y1="6" x2="16" y2="6"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="12" y2="14"></line>
              </svg>
            </div>
            <div>
              <div class="token-card-title">IKEDC</div>
              <div class="token-card-sub">45446644858574747474573</div>
            </div>
          </div>
          <div class="token-card-right">
            <div class="token-card-time">Yesterday 12:00pm</div>
            <div class="token-card-amount">NGN 2,000</div>
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
      <a href="{{ route('reprint-token') }}" class="bottom-nav-item active">
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

    <!-- Token Filter Bottom Sheet Modal -->
    <div id="token-filter-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 24px 24px 36px; gap: 22px;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>

        <!-- Filter section 1: Meter Type -->
        <div class="form-group" style="margin-top: 5px;">
          <label class="form-label" style="color: #64748B; font-weight: 600; font-size: 0.85rem;">Filter by meter type</label>
          <div class="filter-pills-row" style="margin-top: 10px;">
            <button type="button" class="filter-pill active">All</button>
            <button type="button" class="filter-pill">Momas</button>
            <button type="button" class="filter-pill">Others</button>
          </div>
        </div>

        <div style="border-bottom: 1px solid #F1F5F9; margin: 2px 0;"></div>

        <!-- Filter section 2: Date Range -->
        <div class="form-group">
          <label class="form-label" style="color: #64748B; font-weight: 600; font-size: 0.85rem;">Filter by date</label>
          <div style="display: flex; gap: 14px; margin-top: 8px;">
            <div class="form-group" style="flex: 1;">
              <span style="font-size: 0.78rem; font-weight: 700; color: #1E293B; margin-bottom: 4px;">Start Date</span>
              <input type="text" class="input-field" value="12/06/2024" style="height: 52px; border-radius: 16px; border-color: #CBD5E1; padding: 0 16px; font-weight: 700; font-size: 0.95rem; color: #1E293B;">
            </div>
            <div class="form-group" style="flex: 1;">
              <span style="font-size: 0.78rem; font-weight: 700; color: #1E293B; margin-bottom: 4px;">End Date</span>
              <input type="text" class="input-field" value="14/06/2024" style="height: 52px; border-radius: 16px; border-color: #CBD5E1; padding: 0 16px; font-weight: 700; font-size: 0.95rem; color: #1E293B;">
            </div>
          </div>
        </div>

        <!-- Submit Filter Button -->
        <button type="button" id="close-filter-btn" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 12px; width: 100%; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">CONTINUE</button>

      </div>
    </div>


  </main>
@endsection
