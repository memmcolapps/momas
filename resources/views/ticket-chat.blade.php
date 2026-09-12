@extends('layouts.app')

@section('title', 'Ticket Chat - MomasPay Plus')
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
    <div class="form-content" style="padding-top: 20px; gap: 14px; flex: 1;">
      
      <!-- Top Floating Header Card -->
      <div class="make-payment-header-card" style="margin-top: 0; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 14px;">
          <a href="{{ route('support-tickets') }}" class="back-btn" aria-label="Go back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
          </a>
          <h3 style="font-size: 1.1rem; font-weight: 800; color: #1E293B;">Support</h3>
        </div>

        <!-- Chat State Toggle Buttons for Testing Figma States -->
        <div style="display: flex; gap: 4px;">
          <button type="button" id="btn-state-active" class="state-tab-btn active" style="background: #00A859; color: #fff; border: none; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 8px; cursor: pointer;">Active</button>
          <button type="button" id="btn-state-typing" class="state-tab-btn" style="background: #E2E8F0; color: #475569; border: none; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 8px; cursor: pointer;">Typing</button>
          <button type="button" id="btn-state-closed" class="state-tab-btn" style="background: #E2E8F0; color: #475569; border: none; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 8px; cursor: pointer;">Closed</button>
        </div>
      </div>

      <!-- Ticket Chat Card Container -->
      <div class="chat-container-card" style="flex: 1;">
        
        <!-- Ticket Info Header Card -->
        <div style="display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
          <div style="width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #1E293B; display: flex; align-items: center; justify-content: center; color: #1E293B;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div>
            <div style="font-size: 0.95rem; font-weight: 800; color: #00A859;">Meter Issue</div>
            <div style="font-size: 0.7rem; color: #64748B; font-weight: 500;">Date: 20-05-2026</div>
          </div>
        </div>

        <!-- Live Messages List Scroll Container -->
        <div id="chat-messages-container" style="display: flex; flex-direction: column; gap: 14px; max-height: 420px; overflow-y: auto; padding-right: 4px;">
          
          <!-- Message 1: User (Green) -->
          <div class="chat-bubble-user">
            My meter is not accepting credit token. Help!
            <div class="chat-timestamp">3 mins ago</div>
          </div>

          <!-- Message 2: Support Agent (White) -->
          <div class="chat-bubble-agent">
            Kindly confirm that you punched the right token and also provide me with the token used and your meter number so I can assist you further.
            <div class="chat-timestamp">5 secs ago</div>
          </div>

          <!-- Message 3: User (Green) -->
          <div class="chat-bubble-user" id="msg-user-token">
            Token: 21456397701236547898
            <div class="chat-timestamp">3 mins ago</div>
          </div>

          <!-- Message 4: Support Agent (White) -->
          <div class="chat-bubble-agent" id="msg-agent-tamper">
            From what I see here, your meter has been disconnected for tamper. You need to request for a clear tamper token to be connected back.
            <div class="chat-timestamp">now</div>
          </div>

          <!-- Message 5: Support Agent Closed Ticket Message (Hidden in Active state) -->
          <div class="chat-bubble-agent" id="msg-agent-closed" style="display: none;">
            Issue has been resoled. I will be closing this ticket now.
          </div>

          <!-- Typing Indicator Bubble (Hidden in Active state) -->
          <div class="chat-bubble-typing" id="msg-typing-indicator" style="display: none;">
            <div class="chat-typing-dots">
              <span></span>
              <span></span>
              <span></span>
            </div>
            <div class="typing-subtext">typing</div>
          </div>

        </div>

      </div>

      <!-- Bottom Chat Input Bar -->
      <div id="active-chat-input-bar" class="chat-input-bar-row">
        <input type="text" id="chat-msg-input" class="chat-input-field" placeholder="Type a message">
        <button type="button" id="send-chat-msg-btn" class="chat-send-btn" aria-label="Send message">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>

      <!-- Closed Chat Disabled Banner (Hidden by default) -->
      <div id="closed-chat-banner" class="chat-disabled-banner" style="display: none;">
        chat disabled
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
      <a href="{{ route('support') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
  </nav>

  </main>

  
  <script>
    // State Tab Switcher for Figma Screen previews
    const btnActive = document.getElementById('btn-state-active');
    const btnTyping = document.getElementById('btn-state-typing');
    const btnClosed = document.getElementById('btn-state-closed');

    const inputBar = document.getElementById('active-chat-input-bar');
    const closedBanner = document.getElementById('closed-chat-banner');
    const userTokenMsg = document.getElementById('msg-user-token');
    const agentTamperMsg = document.getElementById('msg-agent-tamper');
    const agentClosedMsg = document.getElementById('msg-agent-closed');
    const typingIndicator = document.getElementById('msg-typing-indicator');

    function setActiveTab(activeBtn) {
      [btnActive, btnTyping, btnClosed].forEach(btn => {
        btn.style.background = '#E2E8F0';
        btn.style.color = '#475569';
      });
      activeBtn.style.background = '#00A859';
      activeBtn.style.color = '#FFFFFF';
    }

    btnActive.addEventListener('click', () => {
      setActiveTab(btnActive);
      userTokenMsg.style.display = 'block';
      agentTamperMsg.style.display = 'block';
      agentClosedMsg.style.display = 'none';
      typingIndicator.style.display = 'none';
      inputBar.style.display = 'flex';
      closedBanner.style.display = 'none';
    });

    btnTyping.addEventListener('click', () => {
      setActiveTab(btnTyping);
      userTokenMsg.style.display = 'none';
      agentTamperMsg.style.display = 'none';
      agentClosedMsg.style.display = 'none';
      typingIndicator.style.display = 'flex';
      inputBar.style.display = 'flex';
      closedBanner.style.display = 'none';
    });

    btnClosed.addEventListener('click', () => {
      setActiveTab(btnClosed);
      userTokenMsg.style.display = 'block';
      agentTamperMsg.style.display = 'block';
      agentClosedMsg.style.display = 'block';
      typingIndicator.style.display = 'none';
      inputBar.style.display = 'none';
      closedBanner.style.display = 'block';
    });
  </script>
@endsection
