@extends('layouts.admin')

@section('title', 'Admin - POS Merchant - MomasPay Plus')

@section('content')
<div class="admin-section-title">POS Merchant Terminals</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Active POS Vending Merchants</div>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="admin-table-search-wrapper">
                <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-table-search-input" placeholder="Search records..." id="table-search-input">
              </div>
              <a href="{{ route('admin.add-pos-merchant') }}" class="btn-action-view" style="padding: 8px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Register POS Terminal</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Terminal ID</th>
                  <th>Merchant Name</th>
                  <th>Location / Estate</th>
                  <th>Wallet Balance</th>
                  <th>Daily Volume</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#POS-2091</td>
                  <td style="font-weight: 600;">EVE Ventures Agent</td>
                  <td>EVE Estate Main Gate</td>
                  <td style="font-weight: 700; color: #00A859;">₦450,000.00</td>
                  <td>₦1.2M</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-view-pos-merchant.html?id=#POS-2091" class="btn-action-view" style="text-decoration: none; display: inline-block;">View Logs</a></td>
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
