@extends('layouts.admin')

@section('title', 'Admin - View POS Terminal (#POS-2091) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">POS Terminal Logs & Details: #POS-2091</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Inspect agent wallet balance, daily vending volume, and transaction history logs.</p>
          </div>
          <a href="{{ route('admin.pos-merchant') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Terminals
          </a>
        </div>

        <!-- 4-Card Horizontal Executive Metric Row -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              </div>
              <span>Terminal ID</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">#POS-2091</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">EVE Ventures Agent</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <span>Wallet Balance</span>
            </div>
            <div class="admin-metric-value" style="color: #00A859; font-size: 1.45rem;">₦450,000.00</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Threshold Limit: ₦50,000</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              </div>
              <span>Daily Volume</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.45rem;">₦ 1.2M</div>
            <div style="font-size: 0.78rem; color: #6366F1; font-weight: 600;">184 Tokens Vended</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg>
              </div>
              <span>Status</span>
            </div>
            <div style="margin-top: 4px;"><span class="badge-status resolved" style="font-size: 0.85rem; padding: 5px 14px;">Active</span></div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600; margin-top: 4px;">Online (Cellular 4G)</div>
          </div>
        </div>

        <!-- Terminal Vending History Table Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Terminal Vending Transaction Logs</div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Transaction ID</th>
                  <th>Meter Number</th>
                  <th>Customer Name</th>
                  <th>Amount (₦)</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-07 15:10:04</td>
                  <td style="font-weight: 700; color: #1E293B;">#TXN-88041</td>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td>James Doe</td>
                  <td style="font-weight: 700;">₦ 5,000.00</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
                <tr>
                  <td style="font-weight: 600; color: #64748B;">2026-09-07 14:02:18</td>
                  <td style="font-weight: 700; color: #1E293B;">#TXN-88019</td>
                  <td style="font-weight: 700; color: #00A859;">04218849288</td>
                  <td>Mary Deji</td>
                  <td style="font-weight: 700;">₦ 12,500.00</td>
                  <td><span class="badge-status resolved">Success</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
@endsection
