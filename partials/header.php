<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$flashes = get_flashes();
$menuItems = [
    'dashboard' => 'Dashboard',
    'customers' => 'Customers',
    'laundry-types' => 'Laundry Types',
    'outlets' => 'Outlet',
    'employees' => 'Employee',
    'orders' => 'Order',
    'payments' => 'Payment',
    'queues' => 'Queue'
];
$currentPage = $_GET['page'] ?? 'dashboard';
$user = current_user();

$roleMenus = [
    'admin' => array_keys($menuItems),
    'kasir' => ['dashboard', 'customers', 'orders', 'payments', 'queues'],
    'staff' => ['dashboard', 'orders', 'queues']
];

$visibleMenus = $roleMenus[$user['role'] ?? 'admin'] ?? array_keys($menuItems);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize(app_config('app')['name'] ?? 'Laundry App'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .content-wrapper {
            min-height: calc(100vh - 57px);
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if ($user): ?>
                <li class="nav-item d-flex align-items-center mr-2">
                    <span class="nav-link">Halo, <?= sanitize($user['name'] ?? ''); ?> (<?= sanitize($user['role'] ?? ''); ?>)</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=logout" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <span class="nav-link font-weight-bold"><?= sanitize(app_config('app')['name'] ?? 'Laundry'); ?></span>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="?page=dashboard" class="brand-link text-center">
            <span class="brand-text font-weight-light">Laundry Firebase</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <?php foreach ($menuItems as $page => $label): ?>
                        <?php if (!in_array($page, $visibleMenus, true)) { continue; } ?>
                        <li class="nav-item">
                            <a href="?page=<?= $page; ?>" class="nav-link <?= $currentPage === $page ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-circle"></i>
                                <p><?= $label; ?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= $menuItems[$currentPage] ?? 'Dashboard'; ?></h1>
                    </div>
                </div>
                <?php if (!empty($flashes)): ?>
                    <?php foreach ($flashes as $flash): ?>
                        <div class="alert alert-<?= sanitize($flash['type']); ?>">
                            <?= sanitize($flash['message']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
