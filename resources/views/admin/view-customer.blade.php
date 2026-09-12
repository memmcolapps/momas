@extends('layouts.admin')

@section('title', 'Admin - Customer Profile (James Doe) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Customer Profile: James Doe (#CUST-101)</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Inspect customer account details, assigned smart meters, transaction logs, and manage account status.</p>
          </div>
          <a href="{{ route('admin.customer') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Customers
          </a>
        </div>

        <!-- Horizontal Executive Metric Bar -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <span>Customer ID</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">#CUST-101</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">James Doe</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </div>
              <span>Assigned Meter</span>
            </div>
            <div class="admin-metric-value" style="color: #00A859; font-size: 1.45rem;">04218849201</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Single Phase PREPAID</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <span>Verification</span>
            </div>
            <div style="margin-top: 4px;"><span class="badge-status resolved" style="font-size: 0.85rem; padding: 5px 14px;">Verified</span></div>
            <div style="font-size: 0.78rem; color: #059669; font-weight: 600; margin-top: 4px;">NIN: 23415678901</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <span>Total Vended</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.45rem;">₦ 245,000</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">34 Vending Transactions</div>
          </div>

        </div>

        <!-- Quick Customer Actions Bar -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Customer Operations & Account Controls</div>
          </div>
          <div style="padding: 24px; display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
            <button type="button" class="btn-action-view" style="padding: 10px 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="handleResetPassword()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Reset Password
            </button>

            <button type="button" class="btn-modal-cancel" style="padding: 10px 22px; font-weight: 600; background: #ECFDF5; color: #00A859; border-color: #A7F3D0; display: inline-flex; align-items: center; gap: 8px;" onclick="handleSendEmail()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              Send Email Notice
            </button>

            <button type="button" class="btn-modal-cancel" style="padding: 10px 22px; font-weight: 600; background: #F1F5F9; color: #334155; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 8px;" onclick="handleAssignMeter()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              Assign New Meter
            </button>

            <button type="button" class="btn-action-escalate" style="padding: 10px 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" onclick="handleSuspendAccount()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
              Suspend Account
            </button>
          </div>
        </div>

        <!-- Customer Profile Settings Form Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Customer Information & Parameters</div>
          </div>

          <form action="{{ route('admin.customer') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Customer ID</label>
                <input type="text" class="admin-field-input" value="#CUST-101" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Full Name</label>
                <input type="text" class="admin-field-input" value="James Doe" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Phone Number</label>
                <input type="text" class="admin-field-input" value="+234123456789" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Email Address</label>
                <input type="email" class="admin-field-input" value="jamesdoe@email.com" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Meter Number</label>
                <input type="text" class="admin-field-input" value="04218849201" style="font-weight: 700; color: #00A859;">
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
                <label class="admin-field-label">NIN Identity Number</label>
                <input type="text" class="admin-field-input" value="23415678901">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Verification Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Verified" selected>Verified</option>
                  <option value="Unverified">Unverified</option>
                  <option value="Pending">Pending Document</option>
                </select>
              </div>

              <div class="admin-field-group" style="grid-column: 1 / -1;">
                <label class="admin-field-label">House Address / Flat Location</label>
                <input type="text" class="admin-field-input" value="Flat 4B, Block 12, EVE Estate, Ikeja, Lagos">
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Profile Changes
              </button>
              <a href="{{ route('admin.customer') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>

        <!-- Recent Customer Transactions Table Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Recent Vending & Payment Transactions</div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Transaction Ref</th>
                  <th>Meter Number</th>
                  <th>Amount (₦)</th>
                  <th>Units (kWh)</th>
                  <th>Payment Method</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-07 14:22:10</td>
                  <td style="font-weight: 700; color: #1E293B;">TXN-990123</td>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td style="font-weight: 700;">₦ 3,750.00</td>
                  <td>50.00 kWh</td>
                  <td>POS Terminal #402</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-08-25 10:14:00</td>
                  <td style="font-weight: 700; color: #1E293B;">TXN-982410</td>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td style="font-weight: 700;">₦ 5,000.00</td>
                  <td>66.67 kWh</td>
                  <td>Debit Card (Web)</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-08-10 18:45:22</td>
                  <td style="font-weight: 700; color: #1E293B;">TXN-971002</td>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td style="font-weight: 700;">₦ 2,000.00</td>
                  <td>26.67 kWh</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
@endsection
