@extends('layouts.admin')

@section('title', 'Admin - Estate Management - MomasPay Plus')

@section('content')
<div class="admin-section-title">Estate Management</div>

        <!-- Estate Table Card -->
        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Registered Estates List</div>
            
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 440px; justify-content: flex-end;">
              <div class="admin-table-search-wrapper">
                <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search estate name, location...">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
              </div>
              <a href="{{ route('admin.add-estate') }}" class="btn-action-view" style="padding: 8px 16px; flex-shrink: 0; text-decoration: none;">+ Add Estate</a>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Estate ID</th>
                  <th>Estate Name</th>
                  <th>Location / Address</th>
                  <th>Total Meters</th>
                  <th>Tariff Rate</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#EST-01</td>
                  <td style="font-weight: 700;">EVE Estate</td>
                  <td>Lekki Phase 1, Lagos</td>
                  <td>1,240</td>
                  <td>₦68.50 / kWh</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td>
                    <a href="{{ route('admin.edit-estate') }}" class="btn-action-view" style="text-decoration: none;">Edit</a>
                  </td>
                </tr>
                <tr>
                  <td>#EST-02</td>
                  <td style="font-weight: 700;">Hope Estate</td>
                  <td>Ikeja GRA, Lagos</td>
                  <td>850</td>
                  <td>₦72.00 / kWh</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td>
                    <a href="{{ route('admin.edit-estate') }}" class="btn-action-view" style="text-decoration: none;">Edit</a>
                  </td>
                </tr>
                <tr>
                  <td>#EST-03</td>
                  <td style="font-weight: 700;">Kodak Estate</td>
                  <td>Victoria Island, Lagos</td>
                  <td>610</td>
                  <td>₦80.00 / kWh</td>
                  <td><span class="badge-status resolved">Active</span></td>
                  <td>
                    <a href="{{ route('admin.edit-estate') }}" class="btn-action-view" style="text-decoration: none;">Edit</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">3</span> of <span id="showing-total">3</span> entries</div>
            
            <div class="pagination-controls">
              <button type="button" class="btn-paginate" disabled>Previous</button>
              <button type="button" class="btn-paginate active">1</button>
              <button type="button" class="btn-paginate" disabled>Next</button>
            </div>
          </div>
        </div>
@endsection
