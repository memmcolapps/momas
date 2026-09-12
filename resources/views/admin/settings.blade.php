@extends('layouts.admin')

@section('title', 'Admin - System Settings - MomasPay Plus')

@section('content')
<div class="admin-section-title">Admin System Settings</div>

        <div class="admin-content-card">
          <div class="admin-card-header">
            <div class="admin-card-title">General Platform Settings</div>
          </div>

          <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
              <div class="admin-field-group">
                <label class="admin-field-label">Platform Name</label>
                <input type="text" class="admin-field-input" value="MomasPay Plus Portal">
              </div>
              <div class="admin-field-group">
                <label class="admin-field-label">Support Email</label>
                <input type="email" class="admin-field-input" value="support@momaspay.com">
              </div>
              <div class="admin-field-group">
                <label class="admin-field-label">STS Vending Gateway API Key</label>
                <input type="password" class="admin-field-input" value="momas_live_sk_882910482910">
              </div>
              <div class="admin-field-group">
                <label class="admin-field-label">Default Currency</label>
                <input type="text" class="admin-field-input" value="NGN (₦)" readonly>
              </div>
            </div>

            <button type="button" class="btn-action-view" style="padding: 10px 24px; align-self: flex-start; margin-top: 10px;">Save Configurations</button>
          </div>
        </div>
@endsection
