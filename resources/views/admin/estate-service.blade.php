@extends('layouts.admin')

@section('title', 'Admin - Estate Service Directory - MomasPay Plus')

@section('content')
<div class="admin-section-title">Estate Service Directory Management</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Artisan Service Providers</div>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="admin-table-search-wrapper">
                <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-table-search-input" placeholder="Search records..." id="table-search-input">
              </div>
              <a href="{{ route('admin.add-estate-service') }}" class="btn-action-view" style="padding: 8px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">+ Register Artisan</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Provider ID</th>
                  <th>Artisan Name</th>
                  <th>Service Category</th>
                  <th>Phone Number</th>
                  <th>Assigned Estate</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#ART-001</td>
                  <td style="font-weight: 600;">David Adeleke</td>
                  <td>Certified Electrician</td>
                  <td>+2348039201948</td>
                  <td>EVE Estate</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-view-estate-service.html?id=#ART-001" class="btn-action-view" style="text-decoration: none; display: inline-block;">Manage</a></td>
                </tr>
                <tr>
                  <td>#ART-002</td>
                  <td style="font-weight: 600;">Martha Badojo</td>
                  <td>Plumbing Specialist</td>
                  <td>+2348058192840</td>
                  <td>Hope Estate</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="admin-view-estate-service.html?id=#ART-002" class="btn-action-view" style="text-decoration: none; display: inline-block;">Manage</a></td>
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
