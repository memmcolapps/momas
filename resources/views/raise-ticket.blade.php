@extends('layouts.app')

@section('title', 'Raise Support Ticket - MomasPay Plus')
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
        <a href="{{ route('support-tickets') }}" class="back-btn" aria-label="Go back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
        </a>
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Support</h3>
      </div>

      <!-- Subtitle -->
      <p style="font-size: 0.85rem; color: #475569; text-align: center; line-height: 1.45; font-weight: 500; margin-top: 6px; padding: 0 10px;">
        If you are experiencing any issues, please let us know. We will try to solve the as soon as possible.
      </p>

      <!-- Ticket Creation Form Card -->
      <form id="raise-ticket-form" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 20px; padding: 22px 18px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
          
          <!-- Field 1: Issue Type -->
          <div class="form-group">
            <label class="form-label" style="font-weight: 700; color: #1E293B;">Issue Type<span style="color: #EF4444;">*</span></label>
            <div class="input-container" id="open-issue-type-modal" style="cursor: pointer;">
              <input type="text" id="selected-issue-type-input" class="input-field" value="Meter Issue" readonly style="padding-left: 16px; border-color: #00A859; height: 50px; border-radius: 14px; color: #1E293B; font-weight: 700; cursor: pointer;">
              <span class="select-arrow-icon" style="color: #1E293B;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
              </span>
            </div>
          </div>

          <!-- Field 2: Message -->
          <div class="form-group">
            <label class="form-label" style="font-weight: 700; color: #1E293B;">Message</label>
            <textarea class="input-field" placeholder="Type your message here" style="height: 120px; padding: 14px 16px; border-color: #CBD5E1; border-radius: 14px; font-weight: 600; color: #1E293B; outline: none; resize: none;">My meter is not accepting clear tamper. Help!</textarea>
          </div>

        </div>

        <!-- Action Button: Send -->
        <button type="submit" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 4px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">Send</button>
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
      <a href="{{ route('support') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
  </nav>

    <!-- Modal 1: Issue Type Bottom Sheet Modal -->
    <div id="issue-type-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 20px 24px 30px; gap: 16px;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>
        
        <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 10px;">
          <div class="issue-type-option estate-card-item">Meter Issue</div>
          <div class="issue-type-option estate-card-item">Payment Issues</div>
          <div class="issue-type-option estate-card-item">Other Issues</div>
        </div>
      </div>
    </div>

    <!-- Modal 2: Ticket Created Success Bottom Sheet Modal -->
    <div id="ticket-success-modal" class="modal-overlay">
      <div class="bottom-sheet-modal" style="padding: 25px 24px 35px; gap: 20px; align-items: center; text-align: center;">
        <div class="sheet-handle-bar" style="width: 50px; height: 5px; background: #CBD5E1; border-radius: 3px;"></div>

        <!-- Green Check Circle Icon -->
        <div style="width: 76px; height: 76px; border-radius: 50%; background: #00A859; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 168, 89, 0.35); margin-top: 10px;">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>

        <!-- Ticket Number Title -->
        <h3 style="color: #00A859; font-size: 1.25rem; font-weight: 800;">Ticket #136905</h3>

        <!-- Description Message -->
        <p style="font-size: 0.88rem; color: #334155; font-weight: 500; line-height: 1.45; max-width: 280px;">
          Thank you for contacting MomasPay. Your report has been received and our team will review it shortly.
        </p>

        <!-- Home Button -->
        <a href="{{ route('ticket-chat') }}" class="btn-primary" style="height: 52px; border-radius: 18px; width: 100%; display: flex; align-items: center; justify-content: center;" aria-label="Go to Chat">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
        </a>
      </div>
    </div>


  </main>
@endsection
