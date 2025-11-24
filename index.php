<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page = $_GET['page'] ?? 'dashboard';
$allowedPages = [
    'dashboard' => 'pages/dashboard.php',
    'customers' => 'pages/customers.php',
    'laundry-types' => 'pages/laundry_types.php',
    'outlets' => 'pages/outlets.php',
    'employees' => 'pages/employees.php',
    'orders' => 'pages/orders.php',
    'payments' => 'pages/payments.php',
    'queues' => 'pages/queues.php',
    'login' => 'pages/login.php',
    'logout' => 'pages/logout.php'
];

if (!current_user() && $page !== 'login') {
    header('Location: ?page=login');
    exit;
}

include __DIR__ . '/partials/header.php';

if (isset($allowedPages[$page])) {
    include __DIR__ . '/' . $allowedPages[$page];
} else {
    echo '<div class="alert alert-warning">Halaman tidak ditemukan.</div>';
}

include __DIR__ . '/partials/footer.php';
