@extends('layouts.app')

@section('title', 'Service Directory - MomasPay Plus')
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
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Service</h3>
      </div>

      <!-- Subtitle -->
      <p style="font-size: 0.82rem; color: #64748B; font-weight: 500; margin-top: 10px; line-height: 1.4;">Reach out to us for any issues, we are always here to support you</p>

      <!-- Service Directory Search Form -->
      <form style="display: flex; flex-direction: column; gap: 18px;">
        
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

        <!-- Field 2: Choose Services -->
        <div class="form-group">
          <label class="form-label" style="font-weight: 700; color: #1E293B;">Choose Services</label>
          <div class="input-container" id="open-service-modal" style="cursor: pointer;">
            <input type="text" id="selected-service-input" class="input-field" value="Electrician" readonly style="padding-left: 16px; border-color: #00A859; height: 52px; border-radius: 16px; font-weight: 700; color: #1E293B; cursor: pointer;">
          </div>
        </div>

        <!-- Action Button -->
        <button type="button" id="search-artisan-btn" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 4px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">SEARCH</button>

      </form>

      <!-- Results Section: Available Service -->
      <div style="margin-top: 10px;">
        <div style="font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 14px;">Available Service</div>
        
        <!-- Artisan List Cards -->
        <div id="available-services-section" style="display: flex; flex-direction: column; gap: 14px;">
          
          <!-- Provider Item 1 -->
          <div class="artisan-card-item">
            <div class="artisan-left-info">
              <div class="artisan-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
              </div>
              <div>
                <div class="artisan-name-row">
                  <span class="artisan-name">Tunde Akinola</span>
                  <span class="artisan-stars">⭐⭐⭐</span>
                </div>
                <div class="artisan-role">Electrician</div>
              </div>
            </div>
            <a href="tel:08000000000" class="phone-call-btn" aria-label="Call Tunde Akinola">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </a>
          </div>

          <!-- Provider Item 2 -->
          <div class="artisan-card-item">
            <div class="artisan-left-info">
              <div class="artisan-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
              </div>
              <div>
                <div class="artisan-name-row">
                  <span class="artisan-name">Tunde Akinola</span>
                  <span class="artisan-stars">⭐⭐⭐</span>
                </div>
                <div class="artisan-role">Electrician</div>
              </div>
            </div>
            <a href="tel:08000000000" class="phone-call-btn" aria-label="Call Tunde Akinola">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </a>
          </div>

          <!-- Provider Item 3 -->
          <div class="artisan-card-item">
            <div class="artisan-left-info">
              <div class="artisan-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
              </div>
              <div>
                <div class="artisan-name-row">
                  <span class="artisan-name">Tunde Akinola</span>
                  <span class="artisan-stars">⭐⭐⭐</span>
                </div>
                <div class="artisan-role">Electrician</div>
              </div>
            </div>
            <a href="tel:08000000000" class="phone-call-btn" aria-label="Call Tunde Akinola">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00A859" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </a>
          </div>

        </div>

        <!-- Empty State (Shown when search result has no available service) -->
        <div id="empty-service-box" class="empty-service-box" style="display: none;">
          <div class="empty-service-icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span style="position: absolute; bottom: 2px; right: 2px; background: #FFFFFF; border-radius: 50%; color: #00A859; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; border: 1.5px solid #00A859;">✕</span>
          </div>
          <div class="empty-service-text">Service not available</div>
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

    <!-- Modal 1: Choose Estate Bottom Sheet Modal -->
    <div id="estate-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 20px 24px 30px; gap: 16px;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>
        
        <div style="font-size: 0.78rem; color: #64748B; text-align: center; font-weight: 600;">Set your default Estate</div>

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

    <!-- Modal 2: Choose Service Bottom Sheet Modal -->
    <div id="service-picker-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 20px 24px 30px; gap: 16px;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>
        
        <div style="font-size: 0.78rem; color: #64748B; text-align: center; font-weight: 600;">Choose service</div>

        <!-- Search Box -->
        <div class="input-container" style="margin-top: 4px;">
          <span class="input-icon-left" style="color: #00A859;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
          <input type="text" id="service-search" class="input-field" placeholder="" style="padding-left: 48px; border-color: #00A859; height: 50px; border-radius: 16px;">
        </div>

        <!-- Service Options List -->
        <div style="display: flex; flex-direction: column; gap: 12px; max-height: 250px; overflow-y: auto;">
          <div class="service-option-item estate-card-item">ELECTRICIAN</div>
          <div class="service-option-item estate-card-item">PLUMBER</div>
          <div class="service-option-item estate-card-item">CLEANER</div>
          <div class="service-option-item estate-card-item">CARPENTER</div>
          <div class="service-option-item estate-card-item">PAINTER</div>
        </div>

      </div>
    </div>

  </main>
@endsection
