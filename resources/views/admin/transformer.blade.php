@extends('layouts.admin')

@section('title', 'Admin - Transformer Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">Transformer Management</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Transformer Feeders List</div>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="admin-table-search-wrapper">
                <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-table-search-input" placeholder="Search records..." id="table-search-input">
              </div>
              <a href="{{ route('admin.add-transformer') }}" class="btn-action-view" style="padding: 8px 16px; text-decoration: none;">+ Add Transformer</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Transformer ID</th>
                  <th>Estate Name</th>
                  <th>Capacity (KVA)</th>
                  <th>Connected Meters</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#TRF-5001</td>
                  <td style="font-weight: 700;">EVE Estate Phase 1</td>
                  <td>500 KVA</td>
                  <td>420 Meters</td>
                  <td><span class="badge-status resolved">Optimal</span></td>
                  <td><a href="{{ route('admin.view-transformer') }}" class="btn-action-view" style="text-decoration: none;">View Details</a></td>
                </tr>
                <tr>
                  <td>#TRF-5002</td>
                  <td style="font-weight: 700;">Hope Estate Feeder A</td>
                  <td>300 KVA</td>
                  <td>280 Meters</td>
                  <td><span class="badge-status resolved">Optimal</span></td>
                  <td><a href="{{ route('admin.view-transformer') }}" class="btn-action-view" style="text-decoration: none;">View Details</a></td>
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
