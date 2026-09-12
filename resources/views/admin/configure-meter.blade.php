@extends('layouts.admin')

@section('title', 'Admin - Configure Meter 04218849201 - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Configure Meter: #04218849201</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Manage relay parameters, clear tamper tokens, update load thresholds, and view audit history.</p>
          </div>
          <a href="{{ route('admin.meter') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Installed Meters
          </a>
        </div>

        <!-- Meter Overview Stats Bar - Beautiful 4-Card Horizontal Row -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </div>
              <span>Meter Number</span>
            </div>
            <div class="admin-metric-value" style="color: #00A859; font-size: 1.45rem;">04218849201</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Single Phase PREPAID</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <span>Customer</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">James Doe</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">EVE Estate (IKEDC)</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <span>Current Status</span>
            </div>
            <div style="margin-top: 4px;"><span class="badge-status resolved" style="font-size: 0.85rem; padding: 5px 14px;">Active</span></div>
            <div style="font-size: 0.78rem; color: #059669; font-weight: 600; margin-top: 4px;">Relay: Connected (Normal)</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <span>Credit Balance</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.45rem;">42.50 kWh</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Est. Value: ₦ 3,187.50</div>
          </div>

        </div>

        <!-- Meter Quick Commands Bar -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Meter Operations & Remote Commands</div>
          </div>
          <div style="padding: 24px; display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
            <button type="button" class="btn-action-view" style="padding: 10px 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="handleClearTamper()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Clear Tamper Code
            </button>

            <button type="button" class="btn-action-escalate" style="padding: 10px 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="handleDisconnectMeter()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
              Disconnect Relay
            </button>

            <button type="button" class="btn-modal-cancel" style="padding: 10px 22px; font-weight: 600; background: #ECFDF5; color: #00A859; border-color: #A7F3D0; display: inline-flex; align-items: center; gap: 8px;" onclick="handleSyncRelay()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Sync Relay State
            </button>

            <button type="button" class="btn-modal-cancel" style="padding: 10px 22px; font-weight: 600; background: #F1F5F9; color: #334155; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 8px;" onclick="handleIssueToken()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M12 15h0M17 15h0"/></svg>
              Generate Credit Token
            </button>
          </div>
        </div>

        <!-- Meter Configuration Settings Form Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Configuration Parameters & Thresholds</div>
          </div>

          <form action="{{ route('admin.meter') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Meter Number</label>
                <input type="text" class="admin-field-input" value="04218849201" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Customer Name</label>
                <input type="text" class="admin-field-input" value="James Doe" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned DISCO</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="IKEDC" selected>IKEDC (Ikeja Electric)</option>
                  <option value="EKEDC">EKEDC (Eko Electric)</option>
                  <option value="AEDC">AEDC (Abuja Electric)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate" selected>EVE Estate</option>
                  <option value="Hope Estate">Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Tariff Plan</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="R2" selected>R2 - Single Phase (₦75.00 / kWh)</option>
                  <option value="R3">R3 - Three Phase (₦85.00 / kWh)</option>
                  <option value="Band A">Band A - Premium (₦209.50 / kWh)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Max Power Load Cutoff (kW / Amps)</label>
                <input type="text" class="admin-field-input" value="7.5 kW / 30A">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Emergency Credit Limit (kWh)</label>
                <input type="text" class="admin-field-input" value="10.00 kWh">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Low Balance Warning Level (kWh)</label>
                <input type="text" class="admin-field-input" value="5.00 kWh">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Tamper Sensor Protection</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Enabled" selected>Enabled (Auto Disconnect on Cover Removal)</option>
                  <option value="Disabled">Disabled (Log Event Only)</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">AMI Communication Status</label>
                <input type="text" class="admin-field-input" value="Online (GPRS Cellular - Signal 84%)" readonly style="background: #F8FAFC; color: #059669; font-weight: 600;">
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Configuration
              </button>
              <a href="{{ route('admin.meter') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>

        <!-- Recent Audit & Event Log for this Meter -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Recent Event & Vending Audit Log</div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Event Description</th>
                  <th>Triggered By</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-07 14:22:10</td>
                  <td style="font-weight: 700; color: #1E293B;">Token Top-up (50.00 kWh - ₦3,750.00)</td>
                  <td>POS Merchant #402</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-06 09:15:02</td>
                  <td style="font-weight: 700; color: #1E293B;">Remote Relay Connect Command</td>
                  <td>System Auto-Sync</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-01 11:30:45</td>
                  <td style="font-weight: 700; color: #1E293B;">Clear Tamper Token Issued (#4912-8834-0192)</td>
                  <td>Admin (Support)</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
@endsection
