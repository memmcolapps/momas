@extends('layouts.admin')

@section('title', 'Admin - Token Receipt (#TKN-8841) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Token Receipt Preview & Reprint: #TKN-8841</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Inspect STS 20-digit token details, breakdown of charges, and trigger receipt reprints.</p>
          </div>
          <a href="{{ route('admin.meter-token') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Token Logs
          </a>
        </div>

        <!-- 4-Card Horizontal Metric Bar -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 24px;">
          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M12 15h0M17 15h0"/></svg>
              </div>
              <span>Token ID</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.35rem;">#TKN-8841</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Credit Token (PREPAID)</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); color: #6366F1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </div>
              <span>Meter Number</span>
            </div>
            <div class="admin-metric-value" style="color: #00A859; font-size: 1.45rem;">04218849201</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">James Doe (EVE Estate)</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <span>Units Vended</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.45rem;">142.5 kWh</div>
            <div style="font-size: 0.78rem; color: #059669; font-weight: 600;">Total Paid: ₦ 10,687.50</div>
          </div>

          <div class="admin-metric-card" style="flex: 1; min-width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #D97706;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <span>Vended Date</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.15rem;">2025-11-04 13:45</div>
            <div style="font-size: 0.78rem; color: #64748B; font-weight: 600;">Channel: Admin Console</div>
          </div>
        </div>

        <!-- Receipt Print Card -->
        <div class="admin-content-card printable-card" id="receipt-voucher-card" style="margin-top: 24px; max-width: 650px;">
          <div class="admin-card-header no-print" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="admin-card-title">Official Vending Receipt</div>
            <button type="button" class="btn-action-view no-print" style="padding: 8px 18px;" onclick="printCardOnly('receipt-voucher-card')">Print Receipt</button>
          </div>

          <div style="padding: 32px; background: #FFFFFF;">
            <div style="text-align: center; border-bottom: 2px dashed #CBD5E1; padding-bottom: 20px; margin-bottom: 24px;">
              <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 6px;">
                <svg width="42" height="28" viewBox="0 0 100 65" fill="none">
                  <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#00A859"/>
                  <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#00A859"/>
                  <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#00A859"/>
                </svg>
                <span style="font-weight: 900; font-size: 1.45rem; color: #00A859; letter-spacing: -0.02em;">MomasPay</span>
              </div>
              <div style="font-weight: 800; font-size: 1.05rem; color: #1E293B; letter-spacing: 0.5px;">SMART UTILITY VENDING RECEIPT</div>
              <p style="font-size: 0.8rem; color: #64748B; margin-top: 4px;">STS Certified Electricity Vending Voucher</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem;">
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Token Ref ID:</span><span style="font-weight: 700; color: #1E293B;">#TKN-8841</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Meter Number:</span><span style="font-weight: 700; color: #00A859;">04218849201</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Customer Name:</span><span style="font-weight: 600; color: #1E293B;">James Doe</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">DISCO Provider:</span><span style="font-weight: 600; color: #1E293B;">IKEDC (Ikeja Electric)</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Estate Address:</span><span style="font-weight: 600; color: #1E293B;">Flat 4B, Block 12, EVE Estate</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Tariff Rate:</span><span style="font-weight: 600; color: #1E293B;">R2 - Single Phase (₦75.00/kWh)</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Gross Amount Paid:</span><span style="font-weight: 800; color: #00A859;">₦ 10,687.50</span></div>
              <div style="display: flex; justify-content: space-between;"><span style="color: #64748B;">Energy Units Vended:</span><span style="font-weight: 800; color: #1E293B;">142.50 kWh</span></div>
            </div>

            <!-- STS 20-Digit Token Display Box -->
            <div style="margin-top: 28px; background: #ECFDF5; border: 2px solid #00A859; border-radius: 12px; padding: 20px; text-align: center;">
              <div style="font-size: 0.75rem; color: #059669; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">20-Digit STS Token Code</div>
              <div style="font-size: 1.5rem; font-weight: 900; color: #00A859; margin-top: 8px; letter-spacing: 3px;">2145 - 6397 - 7012 - 3654 - 7898</div>
              <div style="font-size: 0.78rem; color: #047857; margin-top: 8px; font-weight: 600;">Key in token code on meter keypad & press ENTER</div>
            </div>
          </div>
        </div>
@endsection
