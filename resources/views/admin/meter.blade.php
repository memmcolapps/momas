@extends('layouts.admin')

@section('title', 'Admin - Meter Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">Meter Inventory & Monitoring</div>

        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Installed Meters Directory</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search meter number, customer...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.register-meter') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Register Meter</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Meter Number</th>
                  <th>Customer Name</th>
                  <th>Meter Type</th>
                  <th>DISCO</th>
                  <th>Estate</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td style="font-weight: 600;">James Doe</td>
                  <td>Single Phase PREPAID</td>
                  <td>IKEDC</td>
                  <td>EVE Estate</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-configure-meter.html?meter=04218849201" class="btn-action-view" style="text-decoration: none; display: inline-block;">Configure</a></td>
                </tr>
                <tr>
                  <td style="font-weight: 700; color: #00A859;">04218849288</td>
                  <td style="font-weight: 600;">Mary Deji</td>
                  <td>Three Phase POSTPAID</td>
                  <td>EKEDC</td>
                  <td>Hope Estate</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-configure-meter.html?meter=04218849288" class="btn-action-view" style="text-decoration: none; display: inline-block;">Configure</a></td>
                </tr>
                <tr>
                  <td style="font-weight: 700; color: #00A859;">04218849312</td>
                  <td style="font-weight: 600;">Jake Leonard</td>
                  <td>Single Phase PREPAID</td>
                  <td>AEDC</td>
                  <td>Kodak Estate</td>
                  <td><span class="badge-status pending">Disconnected</span></td>
                  <td>
                    <div style="display: flex; gap: 6px; align-items: center;">
                      <a href="admin-configure-meter.html?meter=04218849312" class="btn-action-view" style="text-decoration: none; display: inline-block;">Configure</a>
                      <button type="button" class="btn-action-escalate" onclick="handleReconnect('04218849312')">Reconnect</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">3</span> of <span id="showing-total">4,289</span> entries</div>
            
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
