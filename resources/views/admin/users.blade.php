@extends('layouts.admin')

@section('title', 'Admin - User Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">System Admin Users</div>

        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">System Administrator Accounts</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search name, role, email...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.add-user') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Add User</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>User ID</th>
                  <th>Full Name</th>
                  <th>Role / Designation</th>
                  <th>Email Address</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#USR-001</td>
                  <td style="font-weight: 600;">Super Administrator</td>
                  <td><span style="background:#E8F5E9; color:#00A859; padding:2px 8px; border-radius:6px; font-weight:700; font-size:0.75rem;">Super Admin</span></td>
                  <td>admin@momaspay.com</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-edit-user.html?id=#USR-001" class="btn-action-view" style="text-decoration: none; display: inline-block;">Edit Permissions</a></td>
                </tr>
                <tr>
                  <td>#USR-002</td>
                  <td style="font-weight: 600;">Support Desk Manager</td>
                  <td><span style="background:#EEF2FF; color:#6366F1; padding:2px 8px; border-radius:6px; font-weight:700; font-size:0.75rem;">Support Agent</span></td>
                  <td>support.lead@momaspay.com</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-edit-user.html?id=#USR-002" class="btn-action-view" style="text-decoration: none; display: inline-block;">Edit Permissions</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">2</span> of <span id="showing-total">2</span> entries</div>
            
            <div class="pagination-controls">
              <button type="button" class="btn-paginate" disabled>Previous</button>
              <button type="button" class="btn-paginate active">1</button>
              <button type="button" class="btn-paginate" disabled>Next</button>
            </div>
          </div>
        </div>
@endsection
