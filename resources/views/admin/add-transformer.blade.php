@extends('layouts.admin')

@section('title', 'Admin - Add Transformer - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div class="admin-section-title">Add New Transformer Feeder</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Register a new transformer feeder and assign it to an estate</p>
          </div>
          <a href="{{ route('admin.transformer') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Transformers
          </a>
        </div>

        <!-- Add Transformer Form Card -->
        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">Transformer Feeder Details</div>
          </div>

          <form action="{{ route('admin.transformer') }}" method="GET" style="padding: 28px;">
            <div class="admin-fields-grid" style="background: transparent; border: none; padding: 0; gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Transformer ID</label>
                <input type="text" class="admin-field-input" value="#TRF-5003" readonly style="background: #F8FAFC; color: #64748B;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Estate Name *</label>
                <select class="admin-field-input" style="cursor: pointer;" required>
                  <option value="EVE Estate Phase 1" selected>EVE Estate Phase 1</option>
                  <option value="Hope Estate Feeder A">Hope Estate Feeder A</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                  <option value="Gracefield Estate">Gracefield Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Capacity (KVA) *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. 500 KVA" value="500 KVA" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Feeder Code / Name</label>
                <input type="text" class="admin-field-input" placeholder="e.g. FDR-LEKKI-03" value="FDR-LEKKI-03">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Max Meter Limit</label>
                <input type="number" class="admin-field-input" placeholder="e.g. 500" value="500">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Initial Connected Meters</label>
                <input type="number" class="admin-field-input" placeholder="e.g. 150" value="150">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Optimal" selected>Optimal</option>
                  <option value="Warning">Warning</option>
                  <option value="Overloaded">Overloaded</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Substation Location</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Substation Line 3, Lekki Phase 1" value="Substation Line 3, Lekki Phase 1">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Maintenance Engineer</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Engr. Samuel Ade" value="Engr. Samuel Ade">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Engineer Phone Number</label>
                <input type="text" class="admin-field-input" placeholder="e.g. +234 803 998 8771" value="+234 803 998 8771">
              </div>

            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Save Transformer
              </button>
              <a href="{{ route('admin.transformer') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
