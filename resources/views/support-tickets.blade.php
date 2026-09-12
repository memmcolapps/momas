@extends('layouts.app')

@section('title', 'Support Tickets - MomasPay Plus')
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
    <div class="form-content" style="padding-top: 20px; gap: 18px;">
      
      <!-- Top Floating Header Card -->
      <div class="make-payment-header-card" style="margin-top: 0;">
        <a href="{{ route('support') }}" class="back-btn" aria-label="Go back">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
        </a>
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Support</h3>
      </div>

      <!-- Action Row: See All -->
      <div style="display: flex; justify-content: flex-end;">
        <span style="color: #00A859; font-weight: 800; font-size: 0.85rem; cursor: pointer;">see all</span>
      </div>

      <!-- Support Tickets List -->
      <div style="display: flex; flex-direction: column; gap: 16px;">
        
        <!-- Ticket 1: Meter Issue (A new reply) -->
        <a href="{{ route('raise-ticket') }}" class="ticket-card-item status-reply">
          <div class="ticket-header-row">
            <span class="ticket-title">Meter Issue #136905</span>
            <span class="ticket-time">3 mins ago</span>
          </div>
          <p class="ticket-message">Message: My meter is not accepting credit token. Help!</p>
          <div class="ticket-status-row reply">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#EAB308" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            a new reply
          </div>
        </a>

        <!-- Ticket 2: Payment Issue (Pending) -->
        <a href="{{ route('raise-ticket') }}" class="ticket-card-item status-pending">
          <div class="ticket-header-row">
            <span class="ticket-title">Payment Issue #109057</span>
            <span class="ticket-time">05-03-2026</span>
          </div>
          <p class="ticket-message">Message: I made payment for my friend in another estate, the transaction was successful but yhe token wasn't received. Kindly look into it.</p>
          <div class="ticket-status-row pending">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            pending
          </div>
        </a>

        <!-- Ticket 3: Other issue (Resolved) -->
        <a href="{{ route('raise-ticket') }}" class="ticket-card-item status-resolved">
          <div class="ticket-header-row">
            <span class="ticket-title">Other issue #99012</span>
            <span class="ticket-time">02-07-2025</span>
          </div>
          <p class="ticket-message">Message: The pipe in my bathroom is broken and also the light switch in the kitchen is not working.</p>
          <div class="ticket-status-row resolved">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
            resolved
          </div>
        </a>

        <!-- Ticket 4: Other issue (Resolved) -->
        <a href="{{ route('raise-ticket') }}" class="ticket-card-item status-resolved">
          <div class="ticket-header-row">
            <span class="ticket-title">Other issue #99012</span>
            <span class="ticket-time">02-07-2025</span>
          </div>
          <p class="ticket-message">Message: The pipe in my bathroom is broken and also the light switch in the kitchen is not working.</p>
          <div class="ticket-status-row resolved">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
            resolved
          </div>
        </a>

      </div>

      <!-- Action Button: Raise New Ticket -->
      <a href="{{ route('raise-ticket') }}" class="btn-primary" style="height: 56px; border-radius: 20px; font-size: 1.05rem; font-weight: 800; margin-top: 10px; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.4);">Raise New Ticket</a>

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

  </main>
@endsection
