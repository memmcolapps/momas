@extends('layouts.admin')

@section('title', 'Admin - Customer Directory - MomasPay Plus')

@section('content')
<div class="admin-section-title">Customer Directory</div>

        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Registered Customers List</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search customer, phone, meter...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.add-customer') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Register Customer</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Cust ID</th>
                  <th>Full Name</th>
                  <th>Meter Number</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#CUST-101</td>
                  <td style="font-weight: 600;">James Doe</td>
                  <td style="font-weight: 700; color: #00A859;">04218849201</td>
                  <td>+234123456789</td>
                  <td>jamesdoe@email.com</td>
                  <td><span class="badge-status resolved">Verified</span></td>
                  <td><a href="admin-view-customer.html?cust=#CUST-101" class="btn-action-view" style="text-decoration: none; display: inline-block;">View Profile</a></td>
                </tr>
                <tr>
                  <td>#CUST-102</td>
                  <td style="font-weight: 600;">Mary Deji</td>
                  <td style="font-weight: 700; color: #00A859;">04218849288</td>
                  <td>+2348023456789</td>
                  <td>mary.deji@email.com</td>
                  <td><span class="badge-status resolved">Verified</span></td>
                  <td><a href="admin-view-customer.html?cust=#CUST-102" class="btn-action-view" style="text-decoration: none; display: inline-block;">View Profile</a></td>
                </tr>
                <tr>
                  <td>#CUST-103</td>
                  <td style="font-weight: 600;">Flora Agbada</td>
                  <td style="font-weight: 700; color: #00A859;">04218849312</td>
                  <td>+2348195959753</td>
                  <td>fagba@gmail.com</td>
                  <td><span class="badge-status pending">Unverified</span></td>
                  <td><a href="admin-view-customer.html?cust=#CUST-103" class="btn-action-view" style="text-decoration: none; display: inline-block;">View Profile</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">3</span> of <span id="showing-total">3,912</span> entries</div>
            
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
