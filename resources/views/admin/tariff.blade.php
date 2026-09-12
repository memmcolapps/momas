@extends('layouts.admin')

@section('title', 'Admin - Tariff Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">Tariff Configuration</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Active Tariff Plans</div>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="admin-table-search-wrapper">
                <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-table-search-input" placeholder="Search records..." id="table-search-input">
              </div>
              <a href="{{ route('admin.add-tariff') }}" class="btn-action-view" style="padding: 8px 16px; text-decoration: none;">+ Create Tariff Plan</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Tariff Code</th>
                  <th>Classification</th>
                  <th>Rate per kWh</th>
                  <th>DISCO Provider</th>
                  <th>VAT Tax</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#TRF-A1</td>
                  <td style="font-weight: 700;">Residential Band A</td>
                  <td style="font-weight: 700; color: #00A859;">₦209.50 / kWh</td>
                  <td>IKEDC</td>
                  <td>7.5%</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="{{ route('admin.update-tariff') }}" class="btn-action-view" style="text-decoration: none;">Update Rate</a></td>
                </tr>
                <tr>
                  <td>#TRF-B1</td>
                  <td style="font-weight: 700;">Commercial Band B</td>
                  <td style="font-weight: 700; color: #00A859;">₦185.00 / kWh</td>
                  <td>EKEDC</td>
                  <td>7.5%</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td><a href="{{ route('admin.update-tariff') }}" class="btn-action-view" style="text-decoration: none;">Update Rate</a></td>
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
