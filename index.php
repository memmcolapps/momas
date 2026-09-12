<?php
/**
 * MomasPay Laravel Blade Engine for Apache / XAMPP
 * Compiles and renders Laravel Blade templates (.blade.php) cleanly.
 */

// Determine base path & request route dynamically
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/momaspay/index.php';
$basePath = rtrim(dirname($scriptName), '/\\');
if (empty($basePath) || $basePath === '.') {
    $basePath = '/momaspay';
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Strip query parameters
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

// Remove base path prefix if present
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Normalize route path
$route = '/' . trim($requestUri, '/');
if (isset($_GET['route']) && !empty($_GET['route'])) {
    $route = '/' . trim($_GET['route'], '/');
}

// Route mapping table to Blade template files
$routes = [
    '/' => 'index.blade.php',
    '/index' => 'index.blade.php',
    '/login' => 'login.blade.php',
    '/register' => 'register.blade.php',
    '/forgot-password' => 'forgot-password.blade.php',
    '/reset-password' => 'reset-password.blade.php',
    '/forgot-code' => 'forgot-code.blade.php',
    '/code-validation' => 'code-validation.blade.php',
    '/email-verification' => 'email-verification.blade.php',
    '/account-success' => 'account-success.blade.php',
    '/password-success' => 'password-success.blade.php',
    '/dashboard' => 'dashboard.blade.php',
    '/make-payment' => 'make-payment.blade.php',
    '/pay-other-meter' => 'pay-other-meter.blade.php',
    '/payment-success' => 'payment-success.blade.php',
    '/profile' => 'profile.blade.php',
    '/reprint-token' => 'reprint-token.blade.php',
    '/generate-token' => 'generate-token.blade.php',
    '/token-success' => 'token-success.blade.php',
    '/bills' => 'bills.blade.php',
    '/airtime' => 'airtime.blade.php',
    '/airtime-success' => 'airtime-success.blade.php',
    '/service' => 'service.blade.php',
    '/analytics' => 'analytics.blade.php',
    '/support' => 'support.blade.php',
    '/support-tickets' => 'support-tickets.blade.php',
    '/raise-ticket' => 'raise-ticket.blade.php',
    '/ticket-chat' => 'ticket-chat.blade.php',

    # Admin Portal Routes
    '/admin' => 'admin/dashboard.blade.php',
    '/admin/login' => 'admin/login.blade.php',
    '/admin/dashboard' => 'admin/dashboard.blade.php',
    '/admin/profile' => 'admin/profile.blade.php',
    '/admin/settings' => 'admin/settings.blade.php',
    '/admin/estate' => 'admin/estate.blade.php',
    '/admin/add-estate' => 'admin/add-estate.blade.php',
    '/admin/edit-estate' => 'admin/edit-estate.blade.php',
    '/admin/transformer' => 'admin/transformer.blade.php',
    '/admin/add-transformer' => 'admin/add-transformer.blade.php',
    '/admin/view-transformer' => 'admin/view-transformer.blade.php',
    '/admin/tariff' => 'admin/tariff.blade.php',
    '/admin/add-tariff' => 'admin/add-tariff.blade.php',
    '/admin/update-tariff' => 'admin/update-tariff.blade.php',
    '/admin/meter' => 'admin/meter.blade.php',
    '/admin/configure-meter' => 'admin/configure-meter.blade.php',
    '/admin/register-meter' => 'admin/register-meter.blade.php',
    '/admin/customer' => 'admin/customer.blade.php',
    '/admin/add-customer' => 'admin/add-customer.blade.php',
    '/admin/view-customer' => 'admin/view-customer.blade.php',
    '/admin/estate-service' => 'admin/estate-service.blade.php',
    '/admin/add-estate-service' => 'admin/add-estate-service.blade.php',
    '/admin/view-estate-service' => 'admin/view-estate-service.blade.php',
    '/admin/meter-token' => 'admin/meter-token.blade.php',
    '/admin/generate-meter-token' => 'admin/generate-meter-token.blade.php',
    '/admin/view-meter-token' => 'admin/view-meter-token.blade.php',
    '/admin/pos-merchant' => 'admin/pos-merchant.blade.php',
    '/admin/add-pos-merchant' => 'admin/add-pos-merchant.blade.php',
    '/admin/view-pos-merchant' => 'admin/view-pos-merchant.blade.php',
    '/admin/access-token' => 'admin/access-token.blade.php',
    '/admin/add-access-token' => 'admin/add-access-token.blade.php',
    '/admin/view-access-token' => 'admin/view-access-token.blade.php',
    '/admin/users' => 'admin/users.blade.php',
    '/admin/add-user' => 'admin/add-user.blade.php',
    '/admin/edit-user' => 'admin/edit-user.blade.php',
    '/admin/logged-issues' => 'admin/logged-issues.blade.php',
    '/admin/issue-detail' => 'admin/issue-detail.blade.php',
    '/admin/report' => 'admin/report.blade.php',
    '/admin/audits' => 'admin/audits.blade.php',
];

// Fallback to index if route not found
$templateFile = $routes[$route] ?? 'index.blade.php';
$viewPath = __DIR__ . '/resources/views/' . $templateFile;

if (!file_exists($viewPath)) {
    $viewPath = __DIR__ . '/resources/views/index.blade.php';
}

$bladeContent = file_get_contents($viewPath);

// Helper function to resolve asset() URLs
function asset($path) {
    global $basePath;
    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}

// Helper function to resolve route() URLs
function route($name) {
    global $basePath;
    $routeMapNames = [
        'hub' => '/',
        'login' => '/login',
        'register' => '/register',
        'forgot-password' => '/forgot-password',
        'reset-password' => '/reset-password',
        'forgot-code' => '/forgot-code',
        'code-validation' => '/code-validation',
        'email-verification' => '/email-verification',
        'account-success' => '/account-success',
        'password-success' => '/password-success',
        'dashboard' => '/dashboard',
        'make-payment' => '/make-payment',
        'pay-other-meter' => '/pay-other-meter',
        'payment-success' => '/payment-success',
        'profile' => '/profile',
        'reprint-token' => '/reprint-token',
        'generate-token' => '/generate-token',
        'token-success' => '/token-success',
        'bills' => '/bills',
        'airtime' => '/airtime',
        'airtime-success' => '/airtime-success',
        'service' => '/service',
        'analytics' => '/analytics',
        'support' => '/support',
        'support-tickets' => '/support-tickets',
        'raise-ticket' => '/raise-ticket',
        'ticket-chat' => '/ticket-chat',

        'admin.login' => '/admin/login',
        'admin.dashboard' => '/admin/dashboard',
        'admin.profile' => '/admin/profile',
        'admin.settings' => '/admin/settings',
        'admin.estate' => '/admin/estate',
        'admin.add-estate' => '/admin/add-estate',
        'admin.edit-estate' => '/admin/edit-estate',
        'admin.transformer' => '/admin/transformer',
        'admin.add-transformer' => '/admin/add-transformer',
        'admin.view-transformer' => '/admin/view-transformer',
        'admin.tariff' => '/admin/tariff',
        'admin.add-tariff' => '/admin/add-tariff',
        'admin.update-tariff' => '/admin/update-tariff',
        'admin.meter' => '/admin/meter',
        'admin.configure-meter' => '/admin/configure-meter',
        'admin.register-meter' => '/admin/register-meter',
        'admin.customer' => '/admin/customer',
        'admin.add-customer' => '/admin/add-customer',
        'admin.view-customer' => '/admin/view-customer',
        'admin.estate-service' => '/admin/estate-service',
        'admin.add-estate-service' => '/admin/add-estate-service',
        'admin.view-estate-service' => '/admin/view-estate-service',
        'admin.meter-token' => '/admin/meter-token',
        'admin.generate-meter-token' => '/admin/generate-meter-token',
        'admin.view-meter-token' => '/admin/view-meter-token',
        'admin.pos-merchant' => '/admin/pos-merchant',
        'admin.add-pos-merchant' => '/admin/add-pos-merchant',
        'admin.view-pos-merchant' => '/admin/view-pos-merchant',
        'admin.access-token' => '/admin/access-token',
        'admin.add-access-token' => '/admin/add-access-token',
        'admin.view-access-token' => '/admin/view-access-token',
        'admin.users' => '/admin/users',
        'admin.add-user' => '/admin/add-user',
        'admin.edit-user' => '/admin/edit-user',
        'admin.logged-issues' => '/admin/logged-issues',
        'admin.issue-detail' => '/admin/issue-detail',
        'admin.report' => '/admin/report',
        'admin.audits' => '/admin/audits',
    ];

    $path = $routeMapNames[$name] ?? '/';
    return rtrim($basePath, '/') . '/index.php?route=' . ltrim($path, '/');
}

// Compile Blade Directives
preg_match("/@extends\(['\"](.*?)['\"]\)/", $bladeContent, $layoutMatch);
$layoutName = $layoutMatch[1] ?? 'layouts.app';
$layoutPath = __DIR__ . '/resources/views/' . str_replace('.', '/', $layoutName) . '.blade.php';

preg_match("/@section\(['\"]title['\"],\s*['\"](.*?)['\"]\)/", $bladeContent, $titleMatch);
$title = $titleMatch[1] ?? 'MomasPay';

preg_match("/@section\(['\"]body_class['\"],\s*['\"](.*?)['\"]\)/", $bladeContent, $bodyClassMatch);
$bodyClass = $bodyClassMatch[1] ?? '';

preg_match("/@section\(['\"]content['\"]\)(.*?)@endsection/s", $bladeContent, $contentMatch);
$content = $contentMatch[1] ?? $bladeContent;

if (file_exists($layoutPath)) {
    $layoutContent = file_get_contents($layoutPath);
    
    // Replace layout slots
    $output = str_replace("@yield('title', 'MomasPay')", $title, $layoutContent);
    $output = str_replace("@yield('title', 'Admin - MomasPay Plus')", $title, $output);
    $output = str_replace("@yield('body_class')", $bodyClass, $output);
    $output = str_replace("@yield('content')", $content, $output);
    $output = preg_replace("/@stack\(['\"].*?['\"]\)/", "", $output);
} else {
    $output = $content;
}

// Compile Blade asset & route helpers
$output = preg_replace_callback("/\{\{\s*asset\(['\"](.*?)['\"]\)\s*\}\}/", function($m) {
    return asset($m[1]);
}, $output);

$output = preg_replace_callback("/\{\{\s*route\(['\"](.*?)['\"]\)\s*\}\}/", function($m) {
    return route($m[1]);
}, $output);

// Handle request()->routeIs('...')
$output = preg_replace_callback("/\{\{\s*request\(\)->routeIs\(['\"](.*?)['\"]\)\s*\?\s*['\"]active['\"]\s*:\s*['\"]['\"]\s*\}\}/", function($m) use ($route) {
    $checkName = $m[1];
    $currentName = str_replace('/', '.', trim($route, '/'));
    if (strpos($checkName, '*') !== false) {
        $prefix = str_replace('*', '', $checkName);
        return (strpos($currentName, $prefix) === 0) ? 'active' : '';
    }
    return ($currentName === $checkName) ? 'active' : '';
}, $output);

header('Content-Type: text/html; charset=UTF-8');
echo $output;
