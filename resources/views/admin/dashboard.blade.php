@extends('layouts.admin')

@section('title', 'Admin - Dashboard - MomasPay Plus')

@section('content')
<div class="admin-section-title">Admin Dashboard Overview</div>

        <!-- Metric Cards Row -->
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
          <div class="admin-metric-card" style="width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #E8F5E9; color: #00A859;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
              <span>Total Revenue</span>
            </div>
            <div class="admin-metric-value" style="color: #00A859;">₦148.5M</div>
          </div>

          <div class="admin-metric-card" style="width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #EEF2FF; color: #6366F1;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div>
              <span>Active Meters</span>
            </div>
            <div class="admin-metric-value">4,289</div>
          </div>

          <div class="admin-metric-card" style="width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #FEF3C7; color: #D97706;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
              <span>Total Customers</span>
            </div>
            <div class="admin-metric-value">3,912</div>
          </div>

          <div class="admin-metric-card" style="width: 220px;">
            <div class="admin-metric-header">
              <div class="admin-metric-icon" style="background: #FEE2E2; color: #EF4444;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
              <span>Logged Issues</span>
            </div>
            <div class="admin-metric-value">10</div>
          </div>
        </div>

        <!-- Recent Transactions Table Card -->
        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Recent Vending Transactions</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search Txn ID, customer, meter...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.meter-token') }}" style="font-size: 0.78rem; font-weight: 700; color: #00A859; text-decoration: none; flex-shrink: 0;">View All &rarr;</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Txn ID</th>
                  <th>Customer</th>
                  <th>Meter No</th>
                  <th>Disco</th>
                  <th>Amount</th>
                  <th>Token Generated</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#TXN-94810</td>
                  <td style="font-weight: 600;">James Doe</td>
                  <td>04218849201</td>
                  <td>IKEDC</td>
                  <td style="font-weight: 700; color: #00A859;">₦15,000.00</td>
                  <td><code style="background:#F1F5F9; padding:2px 6px; border-radius:4px;">2145-6397-7012</code></td>
                  <td>Today, 14:22</td>
                </tr>
                <tr>
                  <td>#TXN-94809</td>
                  <td style="font-weight: 600;">Mary Deji</td>
                  <td>04218849288</td>
                  <td>EKEDC</td>
                  <td style="font-weight: 700; color: #00A859;">₦25,000.00</td>
                  <td><code style="background:#F1F5F9; padding:2px 6px; border-radius:4px;">8819-2049-1102</code></td>
                  <td>Today, 13:05</td>
                </tr>
                <tr>
                  <td>#TXN-94808</td>
                  <td style="font-weight: 600;">Flora Agbada</td>
                  <td>04218849312</td>
                  <td>AEDC</td>
                  <td style="font-weight: 700; color: #00A859;">₦10,000.00</td>
                  <td><code style="background:#F1F5F9; padding:2px 6px; border-radius:4px;">3391-0941-8841</code></td>
                  <td>Today, 11:40</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">3</span> of <span id="showing-total">14,290</span> entries</div>
            
            <div class="pagination-controls">
              <button type="button" class="btn-paginate" disabled>Previous</button>
              <button type="button" class="btn-paginate active">1</button>
              <button type="button" class="btn-paginate">2</button>
              <button type="button" class="btn-paginate">3</button>
              <button type="button" class="btn-paginate">Next</button>
            </div>
          </div>
        </div>
@endsection
