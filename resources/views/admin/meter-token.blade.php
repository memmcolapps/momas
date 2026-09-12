@extends('layouts.admin')

@section('title', 'Admin - Meter Token History - MomasPay Plus')

@section('content')
<div class="admin-section-title">Meter Token History & Generation</div>

        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Vended Token Logs</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 520px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search token ID, meter, STS token...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.generate-meter-token') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Generate Token</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Token ID</th>
                  <th>Meter Number</th>
                  <th>Customer Name</th>
                  <th>Token Type</th>
                  <th>20-Digit STS Token</th>
                  <th>Units (kWh)</th>
                  <th>Date Vended</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="color: #64748B;">#TKN-8841</td>
                  <td style="color: #475569;">04218849201</td>
                  <td style="font-weight: 700; color: #1E293B;">James Doe</td>
                  <td style="color: #475569;">Credit Token</td>
                  <td><code style="background:#E6F4EA; color:#00A859; padding:4px 10px; border-radius:6px; font-weight:700; font-family: monospace; font-size: 0.9rem;">2145-6397-7012-3654-7898</code></td>
                  <td style="font-weight: 700; color: #1E293B;">142.5 kWh</td>
                  <td style="color: #64748B;">2025-11-04 13:45</td>
                  <td><a href="admin-view-meter-token.html?token=#TKN-8841" class="btn-action-view" style="text-decoration: none; display: inline-block;">Reprint</a></td>
                </tr>
                <tr>
                  <td style="color: #64748B;">#TKN-8842</td>
                  <td style="color: #475569;">04218849312</td>
                  <td style="font-weight: 700; color: #1E293B;">Jake Leonard</td>
                  <td><span style="color:#D93025; font-weight:700;">Clear Tamper Token</span></td>
                  <td><code style="background:#FCE8E6; color:#D93025; padding:4px 10px; border-radius:6px; font-weight:700; font-family: monospace; font-size: 0.9rem;">9941-0023-4412-8819-0012</code></td>
                  <td style="color: #64748B;">N/A (Reset)</td>
                  <td style="color: #64748B;">2025-11-04 14:10</td>
                  <td><a href="admin-view-meter-token.html?token=#TKN-8842" class="btn-action-view" style="text-decoration: none; display: inline-block;">Reprint</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">2</span> of <span id="showing-total">14,290</span> entries</div>
            
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
