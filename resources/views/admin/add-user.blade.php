@extends('layouts.admin')

@section('title', 'Admin - Add New System User - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Add New System Admin User</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Provision administrator user accounts, assign roles, and set access permissions</p>
          </div>
          <a href="{{ route('admin.users') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Users
          </a>
        </div>

        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">User Account Details & Role Assignment</div>
          </div>

          <form action="{{ route('admin.users') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">User ID</label>
                <input type="text" class="admin-field-input" value="#USR-003" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Full Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Chinedu Okafor" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Email Address *</label>
                <input type="email" class="admin-field-input" placeholder="e.g. c.okafor@momaspay.com" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Role / Designation *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Super Admin">Super Administrator</option>
                  <option value="Support Agent" selected>Support Desk Agent</option>
                  <option value="Finance Auditor">Finance & Vending Auditor</option>
                  <option value="Estate Manager">Estate Regional Manager</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Department</label>
                <input type="text" class="admin-field-input" value="Customer Operations" placeholder="e.g. Operations">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Account Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Suspended">Suspended</option>
                </select>
              </div>

            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Create Admin User
              </button>
              <a href="{{ route('admin.users') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
