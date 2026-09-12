@extends('layouts.app')

@section('title', 'MomasPay - Figma Screen Showcase Hub')
@section('body_class', 'hub-page')

@section('content')
<!-- Page Navigation Bar -->
  <nav class="page-nav-bar">
    <a href="{{ route('hub') }}" class="active">Hub Overview</a>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('make-payment') }}">Momas Meter</a>
    <a href="{{ route('pay-other-meter') }}">Other Meter</a>
    <a href="{{ route('reprint-token') }}">Reprint Token</a>
    <a href="{{ route('generate-token') }}">Generate Token</a>
    <a href="{{ route('service') }}">Services</a>
    <a href="{{ route('bills') }}">Bills</a>
    <a href="{{ route('analytics') }}">Analytics</a>
    <a href="{{ route('support-tickets') }}">Tickets</a>
    <a href="{{ route('support') }}">Support</a>
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('admin.dashboard') }}" style="background: rgba(0, 200, 104, 0.25); color: #34D399; font-weight: 800;">🛡️ Admin Portal</a>
  </nav>






  <!-- Hub Overview Section -->
  <div class="hub-container">
  <!-- Hub Overview Section -->
  <div class="hub-container">
    <div class="hub-header">
      <div style="display: flex; justify-content: center; margin-bottom: 15px;">
        <svg width="70" height="46" viewBox="0 0 100 65" fill="none">
          <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#34D399"/>
          <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#34D399"/>
          <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#34D399"/>
        </svg>
      </div>
      <h1>MomasPay Executive Screen Showcase</h1>
      <p>Interactive directory of all 28 standalone frontend screens for MomasPay Mobile App & Admin Portal Suite</p>

      <!-- Stat Badges Row -->
      <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 16px; margin: 24px 0 28px;">
        <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; padding: 12px 20px; text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #34D399;">28</div>
          <div style="font-size: 0.75rem; color: #94A3B8; font-weight: 700; text-transform: uppercase;">Total Screens</div>
        </div>
        <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; padding: 12px 20px; text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #60A5FA;">16</div>
          <div style="font-size: 0.75rem; color: #94A3B8; font-weight: 700; text-transform: uppercase;">Admin Suite</div>
        </div>
        <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; padding: 12px 20px; text-align: center;">
          <div style="font-size: 1.4rem; font-weight: 800; color: #F59E0B;">12</div>
          <div style="font-size: 0.75rem; color: #94A3B8; font-weight: 700; text-transform: uppercase;">Mobile App</div>
        </div>
      </div>

      <!-- Live Search Bar -->
      <div style="max-width: 460px; margin: 0 auto; position: relative;">
        <svg style="position: absolute; left: 16px; top: 14px; color: #94A3B8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="hub-search-input" placeholder="Search any screen (e.g. Dashboard, Tariff, Ticket, Reprint...)" style="width: 100%; height: 46px; border-radius: 25px; border: 1.5px solid rgba(255,255,255,0.2); background: rgba(15, 23, 42, 0.8); color: #FFFFFF; padding: 0 20px 0 46px; font-size: 0.9rem; outline: none; transition: all 0.25s ease;">
      </div>
    </div>

    <!-- Category 1: Dashboard & Meter Payment Flow -->
    <div style="margin-top: 10px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        ⚡ Dashboard & Meter Payment Flow
      </h2>
      <div class="hub-grid">

        <a href="{{ route('dashboard') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>MomasPay Dashboard</h3>
          <p>Main user home screen with greeting, wallet balance (₦100,000.00), available units (19.3009...), quick action bar, promo slider, and 3x3 services grid.</p>
          <span class="hub-card-btn">Open dashboard.html &rarr;</span>
        </a>

        <a href="{{ route('make-payment') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Make Payment for MOMAS Meter</h3>
          <p>Meter payment screen with meter number verification, user badge (Adejimi Tolulope Adewale), amount field, and gateway payment modal.</p>
          <span class="hub-card-btn">Open make-payment.html &rarr;</span>
        </a>

        <a href="{{ route('pay-other-meter') }}" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Make Payment for Other Meters</h3>
          <p>Pay other meters screen featuring DISCO company search picker modal (IKEDC, EKEDC, AEDC...), meter type dropdown (POSTPAID), beneficiary link & toggle switch.</p>
          <span class="hub-card-btn">Open pay-other-meter.html &rarr;</span>
        </a>

        <a href="{{ route('payment-success') }}" class="hub-card">
          <div class="hub-card-num">4</div>
          <h3>Payment Successful Receipt</h3>
          <p>Paper receipt screen with serrated zigzag edges, Order ID (24063372JM), Name, Address, Service (IKEDC), 20-digit Token, Amount, Home & Share buttons.</p>
          <span class="hub-card-btn">Open payment-success.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 2: Forgot Password Auth Flow -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        🔐 Auth: Forgot Password Flow
      </h2>
      <div class="hub-grid">

        <a href="{{ route('forgot-password') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Forgot Password: Email Verify</h3>
          <p>Email verification screen asking user to enter valid Email / Meter No to receive reset code.</p>
          <span class="hub-card-btn">Open forgot-password.html &rarr;</span>
        </a>

        <a href="{{ route('forgot-code') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Forgot Password: Code Validation</h3>
          <p>OTP code validation screen with 4 digit input boxes, 02:05 timer, and resend email link.</p>
          <span class="hub-card-btn">Open forgot-code.html &rarr;</span>
        </a>

        <a href="{{ route('reset-password') }}" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Reset Password Form</h3>
          <p>Enter new password and confirm password fields with lock icon, eye toggles, and green RESET button.</p>
          <span class="hub-card-btn">Open reset-password.html &rarr;</span>
        </a>

        <a href="{{ route('password-success') }}" class="hub-card">
          <div class="hub-card-num">4</div>
          <h3>Password Reset Success</h3>
          <p>Success screen with light mint green background, green checkmark ring, "Awesome! Your password has been successfully updated" message.</p>
          <span class="hub-card-btn">Open password-success.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 3: Registration & Login Flow -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        📱 Auth: Login & Account Registration Flow
      </h2>
      <div class="hub-grid">
        
        <a href="{{ route('login') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Welcome Back (Login)</h3>
          <p>Login screen with organic wave header gradient, email/meter input, password toggle, forgot password link, and biometric button.</p>
          <span class="hub-card-btn">Open login.html &rarr;</span>
        </a>

        <a href="{{ route('email-verification') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Email Verification</h3>
          <p>Verification screen featuring top back-arrow navigation bar, email input field, and main continue action button.</p>
          <span class="hub-card-btn">Open email-verification.html &rarr;</span>
        </a>

        <a href="#" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Email Notification Template</h3>
          <p>Standalone preview of the notification email received by the user showing the 4-digit code (4567) and disclaimer.</p>
          <span class="hub-card-btn">Open email-template.html &rarr;</span>
        </a>

        <a href="{{ route('code-validation') }}" class="hub-card">
          <div class="hub-card-num">4</div>
          <h3>Code Validation</h3>
          <p>OTP screen with 4 digit input boxes, resend link, live 02:05 countdown timer, and header back button.</p>
          <span class="hub-card-btn">Open code-validation.html &rarr;</span>
        </a>

        <a href="{{ route('register') }}" class="hub-card">
          <div class="hub-card-num">5</div>
          <h3>Set Up Account (Register)</h3>
          <p>Account creation screen with username, meter number, phone number, password, and confirm password fields.</p>
          <span class="hub-card-btn">Open register.html &rarr;</span>
        </a>

        <a href="{{ route('account-success') }}" class="hub-card">
          <div class="hub-card-num">6</div>
          <h3>Account Created Success</h3>
          <p>Success screen with light mint green background, green checkmark ring, "Awesome! Your account has been successfully created" message, and LOGIN button.</p>
          <span class="hub-card-btn">Open account-success.html &rarr;</span>
        </a>

        <a href="{{ route('profile') }}" class="hub-card">
          <div class="hub-card-num">7</div>
          <h3>My Profile & Settings</h3>
          <p>Mobile user profile screen with avatar upload, account information form, linked meter 04218849201 badge, quick links, and logout modal.</p>
          <span class="hub-card-btn">Open profile.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 4: Token History & Support Screens -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        🎫 Token History & Support
      </h2>
      <div class="hub-grid">

        <a href="{{ route('reprint-token') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Reprint Token</h3>
          <p>Token purchase history list screen with search bar, filter funnel modal button, token detail cards (IKEDC, NGN 2,000), and interactive Filter Bottom Sheet Modal.</p>
          <span class="hub-card-btn">Open reprint-token.html &rarr;</span>
        </a>

        <a href="{{ route('support') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Customer Support</h3>
          <p>Support center screen with category cards (Payment Issues, Meter Issues, Other Issues), light green circle badges, and direct contact options.</p>
          <span class="hub-card-btn">Open support.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 5: Access Token Generation Flow -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        🔑 Access Token Generation Flow
      </h2>
      <div class="hub-grid">

        <a href="{{ route('generate-token') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Generate Access Token</h3>
          <p>Form to create visitor access tokens, choose estate picker modal (EVE Estate, Hope Estate, Kodak Estate), flat/address input, and recent token history list.</p>
          <span class="hub-card-btn">Open generate-token.html &rarr;</span>
        </a>

        <a href="{{ route('token-success') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Access Token Created Success</h3>
          <p>Serrated paper receipt screen displaying generated Access Token (4415838), Order ID (24063372JM), visitor details, copy buttons, and share action.</p>
          <span class="hub-card-btn">Open token-success.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 6: Estate Services Directory Flow -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        🛠️ Estate Services Directory Flow
      </h2>
      <div class="hub-grid">

        <a href="{{ route('service') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Estate Services Directory</h3>
          <p>Artisan search screen (Electrician, Plumber, Cleaner...), Choose Estate modal, Choose Service modal, artisan provider cards with call buttons, and empty state support.</p>
          <span class="hub-card-btn">Open service.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 7: Bills Payment & Airtime Flow -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        📱 Bills Payment & Airtime Flow
      </h2>
      <div class="hub-grid">

        <a href="{{ route('bills') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Bills Payment Category</h3>
          <p>Bills hub landing screen featuring category cards for Airtime and Data Bundle purchases across all telecom networks.</p>
          <span class="hub-card-btn">Open bills.html &rarr;</span>
        </a>

        <a href="{{ route('airtime') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Buy Airtime Form</h3>
          <p>Network selector grid (MTN, Glo, Airtel, 9mobile), phone field with phonebook contact icon, amount input, BUY NOW button, and Payment Gateway modal.</p>
          <span class="hub-card-btn">Open airtime.html &rarr;</span>
        </a>

        <a href="{{ route('airtime-success') }}" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Airtime Payment Successful</h3>
          <p>Serrated paper receipt screen showing Order ID (24063372JM), target phone number (08195959753), MTN Nigeria Airtime service, and NGN 1,000 amount.</p>
          <span class="hub-card-btn">Open airtime-success.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 8: Analytics & Ticket Support System -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        📊 Analytics & Support Ticket System
      </h2>
      <div class="hub-grid">

        <a href="{{ route('analytics') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Analytics Dashboard</h3>
          <p>Transaction History line chart, ₦500k total vended record card, Utility Metrics horizontal scroll (Airtime ₦50k, Data ₦30k...), and Access Token donut chart.</p>
          <span class="hub-card-btn">Open analytics.html &rarr;</span>
        </a>

        <a href="{{ route('support-tickets') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Support Ticket List</h3>
          <p>Customer support tickets history with status indicators (a new reply, pending, resolved), left color borders, and Raise New Ticket button.</p>
          <span class="hub-card-btn">Open support-tickets.html &rarr;</span>
        </a>

        <a href="{{ route('raise-ticket') }}" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Raise New Ticket Form</h3>
          <p>Ticket creation form with Issue Type dropdown picker modal (Meter Issues, Payment Issues, Other Issues), message textarea, and Send action button.</p>
          <span class="hub-card-btn">Open raise-ticket.html &rarr;</span>
        </a>

        <a href="{{ route('ticket-chat') }}" class="hub-card">
          <div class="hub-card-num">4</div>
          <h3>Ticket Live Chat & Support</h3>
          <p>Interactive ticket conversation screen featuring active messaging, typing indicator state (••• typing), closed ticket banner (chat disabled), and state toggle tabs.</p>
          <span class="hub-card-btn">Open ticket-chat.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Category 9: Admin Portal Management -->
    <div style="margin-top: 30px;">
      <h2 style="font-size: 1.3rem; color: #34D399; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
        🛡️ Admin Portal Suite (All Sidebar Menu Pages)
      </h2>
      <div class="hub-grid">

        <a href="{{ route('admin.login') }}" class="hub-card">
          <div class="hub-card-num">0</div>
          <h3>Executive Admin Login</h3>
          <p>Executive administrator login page, role selector (Super Admin, Finance, Support), 256-bit SSL security badge, and authentication redirects.</p>
          <span class="hub-card-btn">Open admin-login.html &rarr;</span>
        </a>

        <a href="{{ route('admin.dashboard') }}" class="hub-card">
          <div class="hub-card-num">1</div>
          <h3>Admin Dashboard Overview</h3>
          <p>Main admin stats metrics (Total Revenue ₦148.5M, Active Meters 4,289, Customers 3,912, Issues 10) and recent vending transactions table.</p>
          <span class="hub-card-btn">Open admin-dashboard.html &rarr;</span>
        </a>

        <a href="{{ route('admin.estate') }}" class="hub-card">
          <div class="hub-card-num">2</div>
          <h3>Estate Management Directory</h3>
          <p>Registered estates table (EVE Estate, Hope Estate, Kodak Estate), tariff rates, meter counts, + Add Estate button, and Edit row actions.</p>
          <span class="hub-card-btn">Open admin-estate.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-estate') }}" class="hub-card">
          <div class="hub-card-num">3</div>
          <h3>Add New Estate Form</h3>
          <p>Form to register a new estate on the MomasPay platform with DISCO selection, tariff rate input, manager contacts, and status controls.</p>
          <span class="hub-card-btn">Open admin-add-estate.html &rarr;</span>
        </a>

        <a href="{{ route('admin.edit-estate') }}" class="hub-card">
          <div class="hub-card-num">4</div>
          <h3>Edit Estate Form</h3>
          <p>Update estate details form pre-populated with EVE Estate (#EST-01) metadata, manager phone, DISCO zone, tariff rates, and deactivation actions.</p>
          <span class="hub-card-btn">Open admin-edit-estate.html &rarr;</span>
        </a>

        <a href="{{ route('admin.transformer') }}" class="hub-card">
          <div class="hub-card-num">5</div>
          <h3>Transformer Management Directory</h3>
          <p>Transformer feeders monitoring list (500 KVA, 300 KVA capacity), connected meter counts, + Add Transformer button, and View Details actions.</p>
          <span class="hub-card-btn">Open admin-transformer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-transformer') }}" class="hub-card">
          <div class="hub-card-num">6</div>
          <h3>Add Transformer Feeder Form</h3>
          <p>Form to configure a new transformer feeder line with capacity (KVA), assigned estate, substation location, and engineer details.</p>
          <span class="hub-card-btn">Open admin-add-transformer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-transformer') }}" class="hub-card">
          <div class="hub-card-num">7</div>
          <h3>View Transformer Details (#TRF-5001)</h3>
          <p>Technical specifications inspection view for #TRF-5001 (500 KVA, 420 Connected Meters, 84% Load, Optimal health status, and connected meters list).</p>
          <span class="hub-card-btn">Open admin-view-transformer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.tariff') }}" class="hub-card">
          <div class="hub-card-num">8</div>
          <h3>Tariff Configuration Directory</h3>
          <p>Active tariff plans (Band A ₦209.50/kWh, Band B ₦185.00/kWh), DISCO classifications, + Create Tariff Plan button, and Update Rate actions.</p>
          <span class="hub-card-btn">Open admin-tariff.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-tariff') }}" class="hub-card">
          <div class="hub-card-num">9</div>
          <h3>Create Tariff Plan Form</h3>
          <p>Form to configure a new electricity billing tariff structure with Classification, Rate per kWh (₦), DISCO Provider, and VAT tax percentage.</p>
          <span class="hub-card-btn">Open admin-add-tariff.html &rarr;</span>
        </a>

        <a href="{{ route('admin.update-tariff') }}" class="hub-card">
          <div class="hub-card-num">10</div>
          <h3>Update Tariff Rate Form (#TRF-A1)</h3>
          <p>Update tariff rate configuration form pre-populated with Residential Band A details (₦209.50 / kWh, IKEDC, 7.5% VAT), and deactivation controls.</p>
          <span class="hub-card-btn">Open admin-update-tariff.html &rarr;</span>
        </a>

        <a href="{{ route('admin.meter') }}" class="hub-card">
          <div class="hub-card-num">11</div>
          <h3>Meter Inventory</h3>
          <p>Installed meters directory, single/three-phase classifications, PREPAID/POSTPAID types, and connection status badges.</p>
          <span class="hub-card-btn">Open admin-meter.html &rarr;</span>
        </a>

        <a href="{{ route('admin.register-meter') }}" class="hub-card">
          <div class="hub-card-num">12</div>
          <h3>Register New Meter Form</h3>
          <p>Form to register a new smart meter with Meter Number, Customer Name, Phase Type, DISCO Provider, Estate, and Initial Credit Balance.</p>
          <span class="hub-card-btn">Open admin-register-meter.html &rarr;</span>
        </a>

        <a href="{{ route('admin.configure-meter') }}" class="hub-card">
          <div class="hub-card-num">13</div>
          <h3>Configure Meter (#04218849201)</h3>
          <p>Meter management view with Clear Tamper Code button, Disconnect/Reconnect relay commands, parameter thresholds, and audit log history.</p>
          <span class="hub-card-btn">Open admin-configure-meter.html &rarr;</span>
        </a>

        <a href="{{ route('admin.customer') }}" class="hub-card">
          <div class="hub-card-num">14</div>
          <h3>Customer Directory</h3>
          <p>Registered customer directory (James Doe, Mary Deji...), phone numbers, emails, meter numbers, and verification badges.</p>
          <span class="hub-card-btn">Open admin-customer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-customer') }}" class="hub-card">
          <div class="hub-card-num">15</div>
          <h3>Register New Customer Form</h3>
          <p>Form to register a new customer with Full Name, Phone, Email Address, Meter Mapping, Estate Selection, and NIN Verification.</p>
          <span class="hub-card-btn">Open admin-add-customer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-customer') }}" class="hub-card">
          <div class="hub-card-num">16</div>
          <h3>Customer Profile View (#CUST-101)</h3>
          <p>Detailed customer profile with executive metric cards, Reset Password & Suspend controls, editable profile parameters, and transaction logs.</p>
          <span class="hub-card-btn">Open admin-view-customer.html &rarr;</span>
        </a>

        <a href="{{ route('admin.estate-service') }}" class="hub-card">
          <div class="hub-card-num">17</div>
          <h3>Estate Service Directory</h3>
          <p>Artisan service providers management (Electricians, Plumbers), contact info, assigned estate, and active status.</p>
          <span class="hub-card-btn">Open admin-estate-service.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-estate-service') }}" class="hub-card">
          <div class="hub-card-num">18</div>
          <h3>Register Artisan Form</h3>
          <p>Form to register a new verified artisan with Service Category, Estate Assignment, Contact Phone, and License Ref.</p>
          <span class="hub-card-btn">Open admin-add-estate-service.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-estate-service') }}" class="hub-card">
          <div class="hub-card-num">19</div>
          <h3>Manage Artisan (#ART-001)</h3>
          <p>Artisan management view with rating performance metrics (4.9 ⭐), completed tasks log, and parameter controls.</p>
          <span class="hub-card-btn">Open admin-view-estate-service.html &rarr;</span>
        </a>

        <a href="{{ route('admin.meter-token') }}" class="hub-card">
          <div class="hub-card-num">20</div>
          <h3>Meter Token Logs</h3>
          <p>20-Digit STS vended token logs, credit tokens, clear tamper tokens, kWh units, timestamp logs, and reprint actions.</p>
          <span class="hub-card-btn">Open admin-meter-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.generate-meter-token') }}" class="hub-card">
          <div class="hub-card-num">21</div>
          <h3>Generate STS Token Form</h3>
          <p>Form to vend STS credit tokens and clear tamper tokens with Purchase Amount (₦), calculated kWh units, and STS code preview.</p>
          <span class="hub-card-btn">Open admin-generate-meter-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-meter-token') }}" class="hub-card">
          <div class="hub-card-num">22</div>
          <h3>Token Receipt Preview (#TKN-8841)</h3>
          <p>Official vending receipt preview displaying 20-digit STS token code, gross amount, tariff rates, VAT breakdown, and instant Print trigger.</p>
          <span class="hub-card-btn">Open admin-view-meter-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.pos-merchant') }}" class="hub-card">
          <div class="hub-card-num">21</div>
          <h3>POS Merchant Terminals</h3>
          <p>POS agent vending terminals, terminal IDs, wallet balances, daily vending volumes, and transaction log histories.</p>
          <span class="hub-card-btn">Open admin-pos-merchant.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-pos-merchant') }}" class="hub-card">
          <div class="hub-card-num">22</div>
          <h3>Register POS Terminal Form</h3>
          <p>Provisioning form for new POS vending hardware with Merchant Agent Name, Estate Substation, and Initial Wallet Balance.</p>
          <span class="hub-card-btn">Open admin-add-pos-merchant.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-pos-merchant') }}" class="hub-card">
          <div class="hub-card-num">23</div>
          <h3>POS Terminal Vending Logs (#POS-2091)</h3>
          <p>Terminal details view with agent wallet metrics (₦450k), daily volume (₦1.2M), and token transaction history logs.</p>
          <span class="hub-card-btn">Open admin-view-pos-merchant.html &rarr;</span>
        </a>

        <a href="{{ route('admin.access-token') }}" class="hub-card">
          <div class="hub-card-num">24</div>
          <h3>Access Token Visitor Codes</h3>
          <p>Generated visitor access codes (4415838), visitor names, resident addresses, validity timestamps, and revoke code actions.</p>
          <span class="hub-card-btn">Open admin-access-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-access-token') }}" class="hub-card">
          <div class="hub-card-num">25</div>
          <h3>Generate Visitor Access Code Form</h3>
          <p>Form to issue a new 7-digit visitor pass (4415890) with Visitor Name, Resident Host, Estate Gate, and Validity Window.</p>
          <span class="hub-card-btn">Open admin-add-access-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.view-access-token') }}" class="hub-card">
          <div class="hub-card-num">26</div>
          <h3>Access Code Details (4415838)</h3>
          <p>Visitor pass inspection screen with single-entry metrics, validity status, and instant Revoke Access Code trigger button.</p>
          <span class="hub-card-btn">Open admin-view-access-token.html &rarr;</span>
        </a>

        <a href="{{ route('admin.users') }}" class="hub-card">
          <div class="hub-card-num">27</div>
          <h3>System Admin Users</h3>
          <p>Admin user account management, role designations (Super Admin, Support Agent), permissions, and email directory.</p>
          <span class="hub-card-btn">Open admin-users.html &rarr;</span>
        </a>

        <a href="{{ route('admin.add-user') }}" class="hub-card">
          <div class="hub-card-num">28</div>
          <h3>Add New System User Form</h3>
          <p>Form to create system administrator user accounts with Role Designation, Email Address, and Department assignment.</p>
          <span class="hub-card-btn">Open admin-add-user.html &rarr;</span>
        </a>

        <a href="{{ route('admin.edit-user') }}" class="hub-card">
          <div class="hub-card-num">29</div>
          <h3>Edit User Permissions (#USR-001)</h3>
          <p>System user permissions configuration form with role assignment, department settings, and module privilege checkboxes.</p>
          <span class="hub-card-btn">Open admin-edit-user.html &rarr;</span>
        </a>

        <a href="{{ route('admin.logged-issues') }}" class="hub-card">
          <div class="hub-card-num">12</div>
          <h3>Logged Issue List</h3>
          <p>Support ticket management dashboard matching Figma (Total Logged Issue: 10), status badges (Pending, Resolved), and View/Escalate actions.</p>
          <span class="hub-card-btn">Open admin-logged-issues.html &rarr;</span>
        </a>

        <a href="{{ route('admin.issue-detail') }}" class="hub-card">
          <div class="hub-card-num">13</div>
          <h3>Logged Issue Detail & Reply</h3>
          <p>Customer detail fields, message thread history, reply ticket form, Escalate action, and Close Ticket confirmation modal overlay.</p>
          <span class="hub-card-btn">Open admin-issue-detail.html &rarr;</span>
        </a>

        <a href="{{ route('admin.report') }}" class="hub-card">
          <div class="hub-card-num">14</div>
          <h3>Reports & Financial Summaries</h3>
          <p>Monthly vending reports, total energy kWh, gross vended amounts, earned commissions, and PDF/CSV download actions.</p>
          <span class="hub-card-btn">Open admin-report.html &rarr;</span>
        </a>

        <a href="{{ route('admin.audits') }}" class="hub-card">
          <div class="hub-card-num">15</div>
          <h3>System Audit Logs</h3>
          <p>System audit trail logs, security event timestamps, performing actors, actions taken, target modules, and IP addresses.</p>
          <span class="hub-card-btn">Open admin-audits.html &rarr;</span>
        </a>

        <a href="{{ route('admin.settings') }}" class="hub-card">
          <div class="hub-card-num">16</div>
          <h3>System Settings</h3>
          <p>Platform settings, support emails, STS gateway API keys, currency configurations, and system preferences.</p>
          <span class="hub-card-btn">Open admin-settings.html &rarr;</span>
        </a>

        <a href="{{ route('admin.profile') }}" class="hub-card">
          <div class="hub-card-num">17</div>
          <h3>Admin Account Profile</h3>
          <p>Executive admin profile, avatar upload, contact details, employee ID, security credentials, and 2FA authentication controls.</p>
          <span class="hub-card-btn">Open admin-profile.html &rarr;</span>
        </a>

      </div>
    </div>

    <!-- Hub Footer -->
    <footer class="hub-footer">
      © 2026 - MOMASPAY. All rights reserved.
    </footer>

  </div>

  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const searchInput = document.getElementById('hub-search-input');
      if (searchInput) {
        searchInput.addEventListener('keyup', () => {
          const query = searchInput.value.toLowerCase();
          const cards = document.querySelectorAll('.hub-card');
          cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? 'flex' : 'none';
          });
        });
      }
    });
  </script>
@endsection
