@extends('layouts.admin')

@section('title', 'Admin - Reports & Analytics - MomasPay Plus')

@section('content')
<div class="admin-section-title">Reports & Financial Summaries</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Vending & Revenue Reports</div>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="admin-table-search-wrapper">
                <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-table-search-input" placeholder="Search records..." id="table-search-input">
              </div>
              <button type="button" class="btn-action-view" style="padding: 8px 16px;">📥 Export CSV Report</button>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Report Period</th>
                  <th>Total Transactions</th>
                  <th>Total Energy (kWh)</th>
                  <th>Gross Vended</th>
                  <th>Commission Earned</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: 700;">November 2025</td>
                  <td>14,290 Txns</td>
                  <td>1,840,200 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦148,500,000.00</td>
                  <td style="font-weight: 700;">₦3,712,500.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
                </tr>
                <tr>
                  <td style="font-weight: 700;">October 2025</td>
                  <td>13,950 Txns</td>
                  <td>1,790,500 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦142,300,000.00</td>
                  <td style="font-weight: 700;">₦3,557,500.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
                </tr>
                <tr>
                  <td style="font-weight: 700;">September 2025</td>
                  <td>12,840 Txns</td>
                  <td>1,650,400 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦131,800,000.00</td>
                  <td style="font-weight: 700;">₦3,295,000.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
                </tr>
                <tr>
                  <td style="font-weight: 700;">August 2025</td>
                  <td>15,100 Txns</td>
                  <td>1,920,800 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦156,200,000.00</td>
                  <td style="font-weight: 700;">₦3,905,000.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
                </tr>
                <tr>
                  <td style="font-weight: 700;">July 2025</td>
                  <td>11,400 Txns</td>
                  <td>1,480,100 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦119,500,000.00</td>
                  <td style="font-weight: 700;">₦2,987,500.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
                </tr>
                <tr>
                  <td style="font-weight: 700;">June 2025</td>
                  <td>10,950 Txns</td>
                  <td>1,410,000 kWh</td>
                  <td style="font-weight: 700; color: #00A859;">₦112,000,000.00</td>
                  <td style="font-weight: 700;">₦2,800,000.00</td>
                  <td><button class="btn-action-view">Download PDF</button></td>
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
