@extends('layouts.admin')

@section('title', 'Admin - Logged Issue List - MomasPay Plus')

@section('content')
<div class="admin-section-title">Logged Issue List</div>

        <!-- Metric Counter Card -->
        <div class="admin-metric-card">
          <div class="admin-metric-header">
            <div class="admin-metric-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <span>Total Logged Issue</span>
          </div>
          <div class="admin-metric-value">10</div>
        </div>

        <!-- Logged Issue List Table Card -->
        <div class="admin-content-card">
          <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="admin-card-title">Logged Issue List</div>

            <!-- Search Input Box -->
            <div class="admin-table-search-wrapper">
              <input type="text" id="admin-search-input" class="admin-table-search-input" placeholder="Search ticket ID, name, email...">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="admin-search-icon">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
            </div>
          </div>

          <div class="admin-table-container">
            <table class="admin-table" id="admin-data-table">
              <thead>
                <tr>
                  <th>Ticket ID</th>
                  <th>Customer Name</th>
                  <th>Email</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                
                <!-- Row 1 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">James Doe</td>
                  <td>jamesdoe@email.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Mary Deji</td>
                  <td>jamesdoe@email.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Flora Agbada</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 4 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Jake Leonard</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 5 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">David Adeleke</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 6 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Martha Badojo</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 7 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Nathan Akpofure</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 8 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Peter Jones</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 9 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Sophia Silas</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status resolved">Resolved</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

                <!-- Row 10 -->
                <tr>
                  <td><a href="{{ route('admin.issue-detail') }}" class="ticket-link">#136905</a></td>
                  <td style="font-weight: 600;">Folake Esther</td>
                  <td>fagba@gmail.com</td>
                  <td>2025-11-04 13:45:29</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td>
                    <a href="{{ route('admin.issue-detail') }}" class="btn-action-view">View Issue</a>
                    <button type="button" class="btn-action-escalate">Escalate</button>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>

          <!-- Pagination Bar Footer -->
          <div class="admin-table-pagination">
            <div>Showing <span id="showing-start">1</span> to <span id="showing-end">10</span> of <span id="showing-total">48</span> entries</div>
            
            <div class="pagination-controls">
              <button type="button" class="btn-paginate" disabled>Previous</button>
              <button type="button" class="btn-paginate active">1</button>
              <button type="button" class="btn-paginate">2</button>
              <button type="button" class="btn-paginate">3</button>
              <button type="button" class="btn-paginate">4</button>
              <button type="button" class="btn-paginate">5</button>
              <button type="button" class="btn-paginate">Next</button>
            </div>
          </div>
        </div>

        <!-- Footer Copyright Text -->
@endsection
