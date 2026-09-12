@extends('layouts.admin')

@section('title', 'Admin - Register POS Terminal - MomasPay Plus')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
          <div>
            <div class="admin-section-title">Register POS Merchant Terminal</div>
            <p style="font-size: 0.84rem; color: #64748B; margin-top: 4px;">Provision a new POS vending terminal and assign merchant wallet parameters</p>
          </div>
          <a href="{{ route('admin.pos-merchant') }}" class="btn-modal-cancel" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            &larr; Back to Terminals
          </a>
        </div>

        <div class="admin-content-card" style="margin-top: 24px;">
          <div class="admin-card-header">
            <div class="admin-card-title">POS Terminal Provisioning Form</div>
          </div>

          <form action="{{ route('admin.pos-merchant') }}" method="GET" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

              <div class="admin-field-group">
                <label class="admin-field-label">Terminal ID</label>
                <input type="text" class="admin-field-input" value="#POS-2092" readonly style="background: #F8FAFC; color: #64748B; font-weight: 700;">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Merchant / Agent Name *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Hope Estate Agent Center" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Location / Substation *</label>
                <input type="text" class="admin-field-input" placeholder="e.g. Hope Estate Gatehouse, Ikeja" required>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Assigned Estate *</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="EVE Estate">EVE Estate</option>
                  <option value="Hope Estate" selected>Hope Estate</option>
                  <option value="Kodak Estate">Kodak Estate</option>
                </select>
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Initial Wallet Balance (₦)</label>
                <input type="text" class="admin-field-input" value="₦ 500,000.00">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Commission Rate (%)</label>
                <input type="text" class="admin-field-input" value="1.5%">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">POS Hardware Serial No.</label>
                <input type="text" class="admin-field-input" value="POS-SN-9902148">
              </div>

              <div class="admin-field-group">
                <label class="admin-field-label">Terminal Status</label>
                <select class="admin-field-input" style="cursor: pointer;">
                  <option value="Active" selected>Active</option>
                  <option value="Suspended">Suspended</option>
                  <option value="Pending Clearance">Pending Clearance</option>
                </select>
              </div>

            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 36px; padding-top: 24px; border-top: 1px solid #F1F5F9;">
              <button type="submit" class="btn-action-view" style="padding: 12px 36px; font-size: 0.9rem;">
                Register Terminal
              </button>
              <a href="{{ route('admin.pos-merchant') }}" class="btn-modal-cancel" style="padding: 12px 28px; text-decoration: none; font-size: 0.9rem;">
                Cancel
              </a>
            </div>
          </form>
        </div>
@endsection
