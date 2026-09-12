@extends('layouts.app')

@section('title', 'Analytics - MomasPay Plus')
@section('body_class', '')

@section('content')
<!-- Top Navigation Bar -->
  <!-- Mobile Screen Container -->
  <main class="page-container" style="padding-bottom: 75px; background: #FAFAFA;">

    <!-- Green Header Section -->
    <div style="background: #00A859; padding: 45px 20px 18px; color: #FFFFFF; position: relative; text-align: center;">
      <!-- Status Bar -->
      <div class="status-bar" style="color: #FFFFFF;">
        <span>9:41</span>
        <div style="display: flex; gap: 5px; align-items: center;">
          <svg width="14" height="12" viewBox="0 0 16 12" fill="currentColor">
            <path d="M1 4.5A8.5 8.5 0 0 1 15 4.5M3.5 7.5A5.5 5.5 0 0 1 12.5 7.5M6 10.5A2.5 2.5 0 0 1 10 10.5"/>
          </svg>
          <svg width="15" height="11" viewBox="0 0 18 12" fill="currentColor">
            <path d="M1 10h2V8H1v2zm4 0h2V6H5v4zm4 0h2V4H9v6zm4 0h2V2h-2v8zm4 0h2V0h-2v10z"/>
          </svg>
          <svg width="20" height="10" viewBox="0 0 24 12" fill="currentColor">
            <rect x="1" y="1" width="18" height="10" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
            <rect x="3" y="3" width="14" height="6" rx="1.5"/>
            <path d="M21 4v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
      </div>

      <h3 style="font-size: 1.1rem; font-weight: 800;">Analytics</h3>
    </div>

    <!-- Main Content Area -->
    <div style="padding: 20px; display: flex; flex-direction: column; gap: 22px;">

      <!-- Section 1: Transaction History Line Chart -->
      <div class="analytics-card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
          <h4 style="font-size: 0.95rem; font-weight: 800; color: #1E293B;">Transaction History</h4>
          <select style="border: none; background: transparent; font-size: 0.82rem; font-weight: 700; color: #475569; outline: none; cursor: pointer;">
            <option>This year</option>
            <option>Last year</option>
          </select>
        </div>

        <!-- SVG Line Chart Component -->
        <div style="position: relative; width: 100%; height: 180px;">
          <svg width="100%" height="100%" viewBox="0 0 320 160" preserveAspectRatio="none">
            <!-- Grid Lines -->
            <line x1="30" y1="20" x2="310" y2="20" stroke="#E2E8F0" stroke-dasharray="3 3"/>
            <line x1="30" y1="48" x2="310" y2="48" stroke="#E2E8F0" stroke-dasharray="3 3"/>
            <line x1="30" y1="76" x2="310" y2="76" stroke="#E2E8F0" stroke-dasharray="3 3"/>
            <line x1="30" y1="104" x2="310" y2="104" stroke="#E2E8F0" stroke-dasharray="3 3"/>
            <line x1="30" y1="132" x2="310" y2="132" stroke="#CBD5E1"/>

            <!-- Y Axis Labels -->
            <text x="22" y="24" font-size="9" fill="#94A3B8" text-anchor="end">50</text>
            <text x="22" y="52" font-size="9" fill="#94A3B8" text-anchor="end">40</text>
            <text x="22" y="80" font-size="9" fill="#94A3B8" text-anchor="end">30</text>
            <text x="22" y="108" font-size="9" fill="#94A3B8" text-anchor="end">20</text>
            <text x="22" y="136" font-size="9" fill="#94A3B8" text-anchor="end">0</text>
            
            <!-- Y-Axis Label Rotated -->
            <text x="-80" y="10" transform="rotate(-90)" font-size="9" font-weight="600" fill="#64748B" text-anchor="middle">Amount</text>

            <!-- Line Path -->
            <polyline fill="none" stroke="#6366F1" stroke-width="2" points="
              38,44
              63,115
              88,96
              113,111
              138,84
              163,124
              188,60
              213,118
              238,70
              263,76
              288,24
              305,84
            "/>

            <!-- Data Points Circles -->
            <circle cx="38" cy="44" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="63" cy="115" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="88" cy="96" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="113" cy="111" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="138" cy="84" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="163" cy="124" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="188" cy="60" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="213" cy="118" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="238" cy="70" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="263" cy="76" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="288" cy="24" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
            <circle cx="305" cy="84" r="4" fill="#FFFFFF" stroke="#6366F1" stroke-width="2"/>
          </svg>

          <!-- X Axis Months Labels -->
          <div style="display: flex; justify-content: space-between; padding-left: 30px; font-size: 0.72rem; color: #475569; font-weight: 700; margin-top: 4px;">
            <span>J</span><span>F</span><span>M</span><span>A</span><span>M</span><span>J</span><span>J</span><span>A</span><span>S</span><span>O</span><span>N</span><span>D</span>
          </div>
        </div>
      </div>

      <!-- Section 2: Transaction Record Card -->
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.85rem; font-weight: 700; color: #1E293B;">Filter By:</span>
          <select style="border: none; background: transparent; font-size: 0.82rem; font-weight: 700; color: #475569; outline: none; cursor: pointer;">
            <option>This month</option>
            <option>This week</option>
          </select>
        </div>

        <a href="{{ route('reprint-token') }}" class="analytics-card-box" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none;">
          <div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.88rem; font-weight: 800; color: #1E293B;">
              Transaction Record
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="font-size: 1.6rem; font-weight: 900; color: #0F172A; margin: 6px 0 2px;">₦500k</div>
            <div style="font-size: 0.72rem; color: #94A3B8; font-weight: 500;">Total Amount Vended</div>
          </div>
          <div style="text-align: right;">
            <span class="analytics-metric-badge up">↗ 12%</span>
            <div style="font-size: 0.68rem; color: #94A3B8; margin-top: 4px;">This month</div>
          </div>
        </a>
      </div>

      <!-- Section 3: Utility Metrics -->
      <div>
        <div style="font-size: 0.85rem; font-weight: 700; color: #1E293B; margin-bottom: 10px;">Utility Metrics</div>
        <div class="metrics-horizontal-scroll">
          
          <!-- Metric 1: Airtime -->
          <div class="metric-mini-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 0.78rem; font-weight: 700; color: #1E293B;">Airtime</span>
              <span style="color: #6366F1; font-size: 0.75rem;">✓</span>
            </div>
            <div style="font-size: 1.2rem; font-weight: 900; color: #0F172A;">₦50k</div>
            <div><span class="analytics-metric-badge up">↗ 12%</span></div>
          </div>

          <!-- Metric 2: Data -->
          <div class="metric-mini-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 0.78rem; font-weight: 700; color: #1E293B;">Data</span>
              <span style="color: #6366F1; font-size: 0.75rem;">✓</span>
            </div>
            <div style="font-size: 1.2rem; font-weight: 900; color: #0F172A;">₦30k</div>
            <div><span class="analytics-metric-badge down">↘ 12%</span></div>
          </div>

          <!-- Metric 3: Subscription -->
          <div class="metric-mini-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 0.78rem; font-weight: 700; color: #1E293B;">Subscription</span>
              <span style="color: #6366F1; font-size: 0.75rem;">✓</span>
            </div>
            <div style="font-size: 1.2rem; font-weight: 900; color: #0F172A;">₦50k</div>
            <div><span class="analytics-metric-badge up">↗ 12%</span></div>
          </div>

        </div>
      </div>

      <!-- Section 4: Access Tokens Radial Donut Chart -->
      <div class="analytics-card-box">
        <div style="font-size: 0.85rem; font-weight: 700; color: #1E293B; margin-bottom: 12px;">Access Tokens</div>
        
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <!-- SVG Radial Concentric Rings -->
          <svg width="130" height="130" viewBox="0 0 120 120">
            <!-- Outer Ring Cyan (Failed) -->
            <circle cx="60" cy="60" r="50" fill="none" stroke="#E0F2FE" stroke-width="6"/>
            <circle cx="60" cy="60" r="50" fill="none" stroke="#06B6D4" stroke-width="6" stroke-dasharray="314" stroke-dashoffset="80" stroke-linecap="round"/>

            <!-- Middle Ring Orange (Used) -->
            <circle cx="60" cy="60" r="38" fill="none" stroke="#FFEDD5" stroke-width="6"/>
            <circle cx="60" cy="60" r="38" fill="none" stroke="#F97316" stroke-width="6" stroke-dasharray="238" stroke-dashoffset="90" stroke-linecap="round"/>

            <!-- Inner Ring Red (Pending) -->
            <circle cx="60" cy="60" r="26" fill="none" stroke="#FEE2E2" stroke-width="6"/>
            <circle cx="60" cy="60" r="26" fill="none" stroke="#EF4444" stroke-width="6" stroke-dasharray="163" stroke-dashoffset="100" stroke-linecap="round"/>
          </svg>

          <!-- Chart Legend -->
          <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.78rem; font-weight: 700;">
            <div style="display: flex; align-items: center; gap: 8px; color: #64748B;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444;"></span>
              Pending
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: #64748B;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #F97316;"></span>
              Used
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: #64748B;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #06B6D4;"></span>
              Failed
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav-bar">
      <a href="{{ route('dashboard') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
      </a>
      <a href="{{ route('analytics') }}" class="bottom-nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
      </a>
      <a href="{{ route('support') }}" class="bottom-nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </a>
  </nav>

  </main>
@endsection
