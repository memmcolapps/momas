@extends('layouts.admin')

@section('title', 'Admin - Logged Issue Detail - MomasPay Plus')

@section('content')
<div class="admin-section-title">Logged Issue List</div>

        <!-- Metric Counter Card -->
        <div class="admin-metric-card">
          <div class="admin-metric-header">
            <div class="admin-metric-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <span>Total Logged Issue</span>
          </div>
          <div class="admin-metric-value">10</div>
        </div>

        <!-- Issue Detail Content Card -->
        <div class="admin-content-card">
          
          <!-- Card Header Row with Action Buttons -->
          <div class="admin-card-header">
            <div class="admin-card-title">Logged Issue List</div>

            <div style="display: flex; gap: 8px; align-items: center;">
              <button type="button" id="btn-escalate-action" class="btn-action-escalate" style="margin: 0;">Escalate</button>
              <button type="button" id="btn-open-close-modal" class="btn-action-close">Close Ticket</button>
            </div>
          </div>

          <!-- Customer Metadata Form Grid (Readonly) -->
          <div class="admin-fields-grid">
            <div class="admin-field-group">
              <label class="admin-field-label">First Name</label>
              <input type="text" class="admin-field-input" value="James" readonly>
            </div>
            <div class="admin-field-group">
              <label class="admin-field-label">Last Name</label>
              <input type="text" class="admin-field-input" value="Doe" readonly>
            </div>
            <div class="admin-field-group">
              <label class="admin-field-label">Phone</label>
              <input type="text" class="admin-field-input" value="+234123456789" readonly>
            </div>
            <div class="admin-field-group">
              <label class="admin-field-label">Email</label>
              <input type="text" class="admin-field-input" value="jamesdoe@email.com" readonly>
            </div>
          </div>

          <!-- Conversation Thread History -->
          <div class="admin-thread-container">
            
            <!-- Message 1: Customer -->
            <div class="admin-thread-item">
              <div class="admin-thread-header">
                <div class="admin-thread-user">
                  <span>James Doe</span>
                  <span class="admin-thread-ticket-id">#133567</span>
                </div>
                <div class="admin-thread-time">12:25 PM</div>
              </div>
              <div class="admin-thread-msg">
                Hello, my meter is not accepting clear tamper token. Help!
              </div>
            </div>

            <!-- Message 2: Admin -->
            <div class="admin-thread-item" id="msg-admin-reply-1">
              <div class="admin-thread-header">
                <div class="admin-thread-user">
                  <span>Admin</span>
                  <span class="admin-thread-ticket-id">#133567</span>
                </div>
                <div class="admin-thread-time">01:12 PM</div>
              </div>
              <div class="admin-thread-msg">
                Kindly confirm that you punched the right token and also provide me with the token used and your meter number so I can assist you further.
              </div>
            </div>

            <!-- Message 3: Customer -->
            <div class="admin-thread-item" id="msg-customer-reply-2">
              <div class="admin-thread-header">
                <div class="admin-thread-user">
                  <span>James Doe</span>
                  <span class="admin-thread-ticket-id">#133567</span>
                </div>
                <div class="admin-thread-time">01:12 PM</div>
              </div>
              <div class="admin-thread-msg">
                Token: 21456397701236547898
              </div>
            </div>

            <!-- Message 4: Admin -->
            <div class="admin-thread-item" id="msg-admin-reply-3">
              <div class="admin-thread-header">
                <div class="admin-thread-user">
                  <span>Admin</span>
                  <span class="admin-thread-ticket-id">#133567</span>
                </div>
                <div class="admin-thread-time">01:12 PM</div>
              </div>
              <div class="admin-thread-msg">
                From what I see here, your meter has been disconnected for tamper. You need to request for a clear tamper token to be connected back.
              </div>
            </div>

            <!-- Message 5: Admin Closed State Message (Hidden by default, shown when ticket closed) -->
            <div class="admin-thread-item" id="msg-admin-closed" style="display: none;">
              <div class="admin-thread-header">
                <div class="admin-thread-user">
                  <span>Admin</span>
                  <span class="admin-thread-ticket-id">#133567</span>
                </div>
                <div class="admin-thread-time">05:12 PM</div>
              </div>
              <div class="admin-thread-msg">
                Issue has been resoled. I will be closing this ticket now.
              </div>
              <div class="chat-disabled-banner" style="padding: 10px 0 0 0; text-align: center;">
                chat disabled
              </div>
            </div>

            <!-- Reply Form Area (Visible when ticket is active) -->
            <div id="admin-reply-form-section" class="admin-reply-box">
              <label class="admin-field-label">Reply</label>
              <textarea id="reply-text-input" class="admin-reply-textarea" placeholder="Type your reply here."></textarea>
              <button type="button" id="btn-submit-reply" class="admin-reply-btn">Reply Ticket</button>
            </div>

          </div>

        </div>

        <!-- Footer Copyright Text -->
@endsection
