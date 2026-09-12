@extends('layouts.admin')

@section('title', 'Admin - Add New Customer - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Register New Customer</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Fill out the form below to create a new customer account on MomasPay</p>
          </div>
          <a href="{{ route('admin.customer') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Customers
          </a>
        </div>

        <!-- Add Customer Form Card -->
        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Customer Personal & Account Information</div>
          </div>

          <form action="{{ route('admin.customer') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Customer ID</label>
                <input type="text" class="admin-field-input" value="#CUST-104" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Full Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Babatunde Johnson" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Phone Number *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. +234 803 123 4567" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Email Address *</label>
                <input type="email" class="admin-field-input" placeholder="e.g. customer@email.com" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Meter Number</label>
                <input type="text" class="admin-field-input" placeholder="e.g. 04218849355" value="04218849355" style="font-weight: 700; color: #00A859;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate" selected>EVE Estate</option>
                  <option value="Hope Estate">Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                  <option value="Gracefield Estate Phase 2">Gracefield Estate Phase 2</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">National Identity (NIN)</label>
                <input type="text" class="admin-field-input" placeholder="e.g. 23415678901" value="23415678901">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Verification Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Verified" selected>Verified</option>
                  <option value="Unverified">Unverified</option>
                  <option value="Pending Document">Pending Document</option>
                </select>
              </div>

              <div class="admin-field-group" style="grid-column: 1 / -1;">
                <label class="admin-field-label">House Address / Flat Location</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Flat 3C, Block 5, EVE Estate, Ikeja, Lagos" value="Flat 3C, Block 5, EVE Estate, Ikeja, Lagos">
              </div>

            </div>

            <!-- Form Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Register Customer
              </button>
              <a href="{{ route('admin.customer') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
