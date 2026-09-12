@extends('layouts.admin')

@section('title', 'Admin - Generate Visitor Access Code - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Generate Visitor Access Token</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Issue a temporary 7-digit security entrance code for estate visitors</p>
          </div>
          <a href="{{ route('admin.access-token') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Access Codes
          </a>
        </div>

        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">Visitor Pass Details</div>
          </div>

          <form action="{{ route('admin.access-token') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Access Code</label>
                <input type="text" class="admin-field-input" value="4415890" readonly style="background: #ECFDF5; color: #00A859; font-weight: 800; letter-spacing: 2px;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Visitor Full Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Adejimi Tolulope" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Resident Name / Host *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. James Doe" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Estate *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Hope Estate" selected>Hope Estate</option>
                  <option value="EVE Estate">EVE Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Flat / House Number *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Flat 4B, Hope Estate" value="Flat 4B, Hope Estate" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Validity Window</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Today" selected>Valid Until Today, 23:59</option>
                  <option value="24 Hours">24 Hours Single Entry</option>
                  <option value="7 Days">7 Days Multiple Entry</option>
                </select>
              </div>

            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Generate Access Code
              </button>
              <a href="{{ route('admin.access-token') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
