@extends('layouts.admin')

@section('title', 'Admin - View Transformer (#TRF-5001) - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
          <div>
            <div class="admin-section-title">Transformer Details — #TRF-5001</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">EVE Estate Phase 1 Feeder Lines & Metric Status</p>
          </div>
          <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.add-transformer') }}" class="btn-action-view" style="padding: 10px 20px; text-decoration: none;">Edit Transformer</a>
            <a href="{{ route('admin.transformer') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
              &larr; Back to List
            </a>
          </div>
        </div>

        <!-- Metric Stat Cards -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
          <div class="admin-metric-card" style="width: auto; flex: 1;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/></svg>
              </div>
              <span>Capacity</span>
            </div>
            <div class="admin-metric-value">500 KVA</div>
          </div>

          <div class="admin-metric-card" style="width: auto; flex: 1;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #E0F2FE; color: #0284C7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </div>
              <span>Connected Meters</span>
            </div>
            <div class="admin-metric-value">420 / 500</div>
          </div>

          <div class="admin-metric-card" style="width: auto; flex: 1;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #FEF3C7; color: #D97706;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
              <span>Current Load</span>
            </div>
            <div class="admin-metric-value">84% Load</div>
          </div>

          <div class="admin-metric-card" style="width: auto; flex: 1;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <span>Feeder Health</span>
            </div>
            <div class="admin-metric-value" style="font-size: 1.3rem;">
              <span class="badge-status resolved">Optimal</span>
            </div>
          </div>
        </div>

        <!-- Specifications & Connected Meters Cards -->
        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Feeder Technical Specifications</div>
          </div>

          <div class="admin-fields-grid" style="background: transparent; border: none; padding: 26px 28px; gap: 24px;">
            <div class="admin-field-group">
              <label class="admin-field-label">Transformer ID</label>
              <input type="text" class="admin-field-input" value="#TRF-5001" readonly style="background: #F8FAFC; color: #64748B;">
            </div>

            <div class="admin-field-group">
              <label class="admin-field-label">Assigned Estate</label>
              <input type="text" class="admin-field-input" value="EVE Estate Phase 1" readonly style="background: #F8FAFC; color: #64748B;">
            </div>

            <div class="admin-field-group">
              <label class="admin-field-label">Capacity (KVA)</label>
              <input type="text" class="admin-field-input" value="500 KVA" readonly style="background: #F8FAFC; color: #64748B;">
            </div>

            <div class="admin-field-group">
              <label class="admin-field-label">Feeder Code</label>
              <input type="text" class="admin-field-input" value="FDR-LEKKI-01" readonly style="background: #F8FAFC; color: #64748B;">
            </div>

            <div class="admin-field-group">
              <label class="admin-field-label">Substation Location</label>
              <input type="text" class="admin-field-input" value="Substation Alpha 1, Lekki" readonly style="background: #F8FAFC; color: #64748B;">
            </div>

            <div class="admin-field-group">
              <label class="admin-field-label">Maintenance Engineer</label>
              <input type="text" class="admin-field-input" value="Engr. Samuel Ade (+234 803 998 8771)" readonly style="background: #F8FAFC; color: #64748B;">
            </div>
          </div>
        </div>

        <!-- Connected Meters Preview Table -->
        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Connected Meters on #TRF-5001</div>
            <a href="{{ route('admin.meter') }}" class="ticket-link" style="font-size: 0.84rem;">View All Meters &rarr;</a>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Meter Number</th>
                  <th>Customer Name</th>
                  <th>Meter Phase</th>
                  <th>Meter Type</th>
                  <th>Vending Balance</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#04218849312</td>
                  <td style="font-weight: 700;">Adejimi Tolulope Adewale</td>
                  <td>Single Phase</td>
                  <td>PREPAID</td>
                  <td>₦12,450.00</td>
                  <td><span class="badge-status resolved">Connected</span></td>
                </tr>
                <tr>
                  <td>#04218849313</td>
                  <td style="font-weight: 700;">Mary Deji</td>
                  <td>Three Phase</td>
                  <td>PREPAID</td>
                  <td>₦45,000.00</td>
                  <td><span class="badge-status resolved">Connected</span></td>
                </tr>
                <tr>
                  <td>#04218849314</td>
                  <td style="font-weight: 700;">John Doe</td>
                  <td>Single Phase</td>
                  <td>POSTPAID</td>
                  <td>₦8,200.00</td>
                  <td><span class="badge-status resolved">Connected</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
@endsection
