@extends('layouts.admin')

@section('title', 'Admin - System Audits - MomasPay Plus')

@section('content')
<div class="admin-section-title">System Activity Audit Logs</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Security & Audit History</div>
            <div class="admin-table-search-wrapper">
              <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" class="admin-table-search-input" placeholder="Search audit logs..." id="table-search-input">
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>User / Actor</th>
                  <th>Action Performed</th>
                  <th>Module</th>
                  <th>IP Address</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2025-11-04 14:10:22</td>
                  <td style="font-weight: 600;">Super Administrator</td>
                  <td>Generated Clear Tamper Token for Meter #04218849312</td>
                  <td>Meter Token</td>
                  <td>192.168.1.104</td>
                </tr>
                <tr>
                  <td>2025-11-04 13:45:10</td>
                  <td style="font-weight: 600;">Support Lead</td>
                  <td>Closed Support Ticket #136905</td>
                  <td>Logged Issues</td>
                  <td>192.168.1.112</td>
                </tr>
                <tr>
                  <td>2025-11-04 12:30:15</td>
                  <td style="font-weight: 600;">Finance Auditor</td>
                  <td>Exported Vending Revenue Summary Report (CSV)</td>
                  <td>Reports</td>
                  <td>192.168.1.108</td>
                </tr>
                <tr>
                  <td>2025-11-04 11:15:00</td>
                  <td style="font-weight: 600;">Estate Manager</td>
                  <td>Updated Tariff Rate Band A to ₦225.00/kWh</td>
                  <td>Tariff Config</td>
                  <td>192.168.1.105</td>
                </tr>
                <tr>
                  <td>2025-11-04 10:05:42</td>
                  <td style="font-weight: 600;">POS Agent Manager</td>
                  <td>Credited POS Merchant Wallet #POS-2091 (+₦500,000.00)</td>
                  <td>POS Merchant</td>
                  <td>192.168.1.120</td>
                </tr>
                <tr>
                  <td>2025-11-04 09:20:18</td>
                  <td style="font-weight: 600;">System Bot</td>
                  <td>Automated Daily STS Key Renewal Backup Executed</td>
                  <td>System Security</td>
                  <td>127.0.0.1</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="admin-table-pagination">
            <div>Showing 1 to 10 of 48 entries</div>
            <div class="pagination-controls">
              <button class="btn-paginate" disabled>&laquo; Prev</button>
              <button class="btn-paginate active">1</button>
              <button class="btn-paginate">2</button>
              <button class="btn-paginate">3</button>
              <button class="btn-paginate">Next &raquo;</button>
            </div>
          </div>
        </div>
@endsection
