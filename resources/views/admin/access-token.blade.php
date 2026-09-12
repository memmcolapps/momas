@extends('layouts.admin')

@section('title', 'Admin - Access Token Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">Visitor Access Code Management</div>

        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Generated Estate Access Tokens</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search access code, visitor...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.add-access-token') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Generate Code</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Access Code</th>
                  <th>Visitor Name</th>
                  <th>Resident / House</th>
                  <th>Estate</th>
                  <th>Valid Until</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><code style="background:#00A859; color:#fff; font-weight:800; padding:4px 10px; border-radius:6px;">4415838</code></td>
                  <td style="font-weight: 600;">Adejimi Tolulope</td>
                  <td>Flat 4B, Hope Estate</td>
                  <td>Hope Estate</td>
                  <td>Today, 23:59</td>
                  <td><span class="badge-status resolved">Valid</span></td>
                  <td>
                    <div style="display: flex; gap: 6px; align-items: center;">
                      <a href="admin-view-access-token.html?code=4415838" class="btn-action-view" style="text-decoration: none; display: inline-block;">Details</a>
                      <button type="button" class="btn-action-escalate">Revoke Code</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">1</span> of <span id="showing-total">1</span> entries</div>
            
            <div class="pagination-controls">
              <button type="button" class="btn-paginate" disabled>Previous</button>
              <button type="button" class="btn-paginate active">1</button>
              <button type="button" class="btn-paginate" disabled>Next</button>
            </div>
          </div>
        </div>
@endsection
