<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MomasPay Laravel Prototype
|--------------------------------------------------------------------------
*/

// Showcase Hub
Route::view('/', 'index')->name('hub');

// Mobile App User Routes
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/forgot-password', 'forgot-password')->name('forgot-password');
Route::view('/reset-password', 'reset-password')->name('reset-password');
Route::view('/forgot-code', 'forgot-code')->name('forgot-code');
Route::view('/code-validation', 'code-validation')->name('code-validation');
Route::view('/email-verification', 'email-verification')->name('email-verification');
Route::view('/account-success', 'account-success')->name('account-success');
Route::view('/password-success', 'password-success')->name('password-success');

Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/make-payment', 'make-payment')->name('make-payment');
Route::view('/pay-other-meter', 'pay-other-meter')->name('pay-other-meter');
Route::view('/payment-success', 'payment-success')->name('payment-success');
Route::view('/profile', 'profile')->name('profile');
Route::view('/reprint-token', 'reprint-token')->name('reprint-token');
Route::view('/generate-token', 'generate-token')->name('generate-token');
Route::view('/token-success', 'token-success')->name('token-success');
Route::view('/bills', 'bills')->name('bills');
Route::view('/airtime', 'airtime')->name('airtime');
Route::view('/airtime-success', 'airtime-success')->name('airtime-success');
Route::view('/service', 'service')->name('service');
Route::view('/analytics', 'analytics')->name('analytics');

// Support Routes
Route::view('/support', 'support')->name('support');
Route::view('/support-tickets', 'support-tickets')->name('support-tickets');
Route::view('/raise-ticket', 'raise-ticket')->name('raise-ticket');
Route::view('/ticket-chat', 'ticket-chat')->name('ticket-chat');

// Admin Portal Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login')->name('login');
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/settings', 'admin.settings')->name('settings');

    // Estate Management
    Route::view('/estate', 'admin.estate')->name('estate');
    Route::view('/add-estate', 'admin.add-estate')->name('add-estate');
    Route::view('/edit-estate', 'admin.edit-estate')->name('edit-estate');

    // Transformer Feeders
    Route::view('/transformer', 'admin.transformer')->name('transformer');
    Route::view('/add-transformer', 'admin.add-transformer')->name('add-transformer');
    Route::view('/view-transformer', 'admin.view-transformer')->name('view-transformer');

    // Tariff Rates
    Route::view('/tariff', 'admin.tariff')->name('tariff');
    Route::view('/add-tariff', 'admin.add-tariff')->name('add-tariff');
    Route::view('/update-tariff', 'admin.update-tariff')->name('update-tariff');

    // Meter Management
    Route::view('/meter', 'admin.meter')->name('meter');
    Route::view('/configure-meter', 'admin.configure-meter')->name('configure-meter');
    Route::view('/register-meter', 'admin.register-meter')->name('register-meter');

    // Customer Accounts
    Route::view('/customer', 'admin.customer')->name('customer');
    Route::view('/add-customer', 'admin.add-customer')->name('add-customer');
    Route::view('/view-customer', 'admin.view-customer')->name('view-customer');

    // Estate Services
    Route::view('/estate-service', 'admin.estate-service')->name('estate-service');
    Route::view('/add-estate-service', 'admin.add-estate-service')->name('add-estate-service');
    Route::view('/view-estate-service', 'admin.view-estate-service')->name('view-estate-service');

    // Meter Token
    Route::view('/meter-token', 'admin.meter-token')->name('meter-token');
    Route::view('/generate-meter-token', 'admin.generate-meter-token')->name('generate-meter-token');
    Route::view('/view-meter-token', 'admin.view-meter-token')->name('view-meter-token');

    // POS Merchants
    Route::view('/pos-merchant', 'admin.pos-merchant')->name('pos-merchant');
    Route::view('/add-pos-merchant', 'admin.add-pos-merchant')->name('add-pos-merchant');
    Route::view('/view-pos-merchant', 'admin.view-pos-merchant')->name('view-pos-merchant');

    // Access Token
    Route::view('/access-token', 'admin.access-token')->name('access-token');
    Route::view('/add-access-token', 'admin.add-access-token')->name('add-access-token');
    Route::view('/view-access-token', 'admin.view-access-token')->name('view-access-token');

    // Admin Users
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/add-user', 'admin.add-user')->name('add-user');
    Route::view('/edit-user', 'admin.edit-user')->name('edit-user');

    // Logged Issues & Support
    Route::view('/logged-issues', 'admin.logged-issues')->name('logged-issues');
    Route::view('/issue-detail', 'admin.issue-detail')->name('issue-detail');

    // Reports & Audits
    Route::view('/report', 'admin.report')->name('report');
    Route::view('/audits', 'admin.audits')->name('audits');
});
