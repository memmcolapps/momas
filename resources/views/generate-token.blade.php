@extends('layouts.app')

@section('title', 'Generate Access Token - MomasPay Plus')
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
        <h3 style="font-size: 1.05rem; font-weight: 800; color: #1E293B;">Generate Access Token</h3>
      </div>

      <!-- Subtitle -->
      <p style="font-size: 0.82rem; color: #64748B; font-weight: 500; margin-top: 10px; line-height: 1.4;">Easily create access token and share with your visitor</p>

      <!-- Token Generation Form -->
      <form action="{{ route('token-success') }}" style="display: flex; flex-direction: column; gap: 18px;">
        
        <!-- Field 1: Choose Estate -->
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label class="form-label" style="font-weight: 700; color: #1E293B;">Choose Estate</label>
            <button type="button" id="change-estate-btn" style="background: none; border: none; color: #00A859; font-weight: 800; font-size: 0.82rem; cursor: pointer;">Change Estate</button>
          </div>
          <div class="input-container" id="open-estate-modal" style="cursor: pointer;">
            <input type="text" id="selected-estate-input" class="input-field" value="EVE ESTATE" readonly style="padding-left: 16px; background: #F0FDF4; border-color: #00A859; color: #1E293B; font-weight: 800; height: 52px; border-radius: 16px; cursor: pointer;">
          </div>
        </div>

        <!-- Field 2: Expected Visitor -->
        <div class="form-group">
          <label class="form-label" style="font-weight: 700; color: #1E293B;">Expected Visitor</label>
          <div class="input-container">
            <input type="number" class="input-field" value="1" min="1" required style="padding-left: 16px; border-color: #00A859; height: 52px; border-radius: 16px; font-weight: 700; color: #1E293B;">
          </div>
        </div>

        <!-- Action Button -->
        <button type="submit" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 6px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">CONTINUE</button>

      </form>

      <!-- Section: Recent Access Token -->
      <div style="margin-top: 15px;">
        <div style="font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 12px;">Recent Access Token</div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
          
          <!-- Recent Item 1 -->
          <a href="{{ route('token-success') }}" class="token-card-item">
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
                <div class="token-card-title">435484</div>
                <div class="token-card-sub">TK2236464</div>
              </div>
            </div>
            <div class="token-card-right">
              <div class="token-card-time">Yesterday 12:00pm</div>
              <div style="margin-top: 3px;">
                <span class="status-pill-badge processed">Processed</span>
              </div>
            </div>
          </a>

          <!-- Recent Item 2 -->
          <a href="{{ route('token-success') }}" class="token-card-item">
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
                <div class="token-card-title">667786</div>
                <div class="token-card-sub">TK2236464</div>
              </div>
            </div>
            <div class="token-card-right">
              <div class="token-card-time">Yesterday 12:00pm</div>
              <div style="margin-top: 3px;">
                <span class="status-pill-badge pending">Pending</span>
              </div>
            </div>
          </a>

        </div>
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

    <!-- Choose Estate Bottom Sheet Modal -->
    <div id="estate-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 20px 24px 30px; gap: 16px;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>
        
        <div style="font-size: 0.78rem; color: #64748B; text-align: center; font-weight: 600;">Set your default address</div>

        <!-- Search Box -->
        <div class="input-container" style="margin-top: 4px;">
          <span class="input-icon-left" style="color: #00A859;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
          <input type="text" id="estate-search" class="input-field" placeholder="" style="padding-left: 48px; border-color: #00A859; height: 50px; border-radius: 16px;">
        </div>

        <!-- Estate Options List -->
        <div style="display: flex; flex-direction: column; gap: 12px; max-height: 200px; overflow-y: auto;">
          <div class="estate-card-item selected">EVE ESTATE</div>
          <div class="estate-card-item">HOPE ESTATE</div>
          <div class="estate-card-item">KODAK ESTATE</div>
        </div>

        <div style="border-bottom: 1px solid #F1F5F9; margin: 4px 0;"></div>

        <!-- Flat / Address Section -->
        <div style="display: flex; gap: 12px;">
          <div class="form-group" style="width: 75px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748B;">FLAT/NO</span>
            <input type="text" class="input-field" value="2" style="height: 48px; border-radius: 14px; border-color: #00A859; padding: 0 12px; font-weight: 700; text-align: center; color: #1E293B;">
          </div>
          <div class="form-group" style="flex: 1;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748B;">Address</span>
            <input type="text" class="input-field" value="Kolade Street" style="height: 48px; border-radius: 14px; border-color: #00A859; padding: 0 14px; font-weight: 700; color: #1E293B;">
          </div>
        </div>

        <!-- Set Default Button -->
        <button type="button" id="close-estate-btn" class="btn-primary" style="height: 54px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 8px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">SET DEFAULT</button>

      </div>
    </div>

  </main>
@endsection
