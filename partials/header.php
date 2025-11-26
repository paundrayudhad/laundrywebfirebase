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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .sidebar-link.active {
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
<div class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="?page=dashboard">Laundry Firebase</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto align-items-center">
                    <?php if ($user): ?>
                        <li class="nav-item mr-2 text-muted">
                            Halo, <?= sanitize($user['name'] ?? ''); ?> (<?= sanitize($user['role'] ?? ''); ?>)
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-secondary btn-sm" href="?page=logout" title="Keluar">
                                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            <aside class="col-md-3 col-lg-2 bg-white border-right py-4">
                <div class="list-group list-group-flush">
                    <?php foreach ($menuItems as $page => $label): ?>
                        <?php if (!in_array($page, $visibleMenus, true)) { continue; } ?>
                        <a href="?page=<?= $page; ?>" class="list-group-item list-group-item-action sidebar-link <?= $currentPage === $page ? 'active' : ''; ?>">
                            <i class="fas fa-circle mr-2 small"></i><?= $label; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <main class="col-md-9 col-lg-10 py-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h4 mb-0"><?= $menuItems[$currentPage] ?? 'Dashboard'; ?></h1>
                    <span class="text-muted font-weight-bold"><?= sanitize(app_config('app')['name'] ?? 'Laundry'); ?></span>
                </div>
                <?php if (!empty($flashes)): ?>
                    <?php foreach ($flashes as $flash): ?>
                        <div class="alert alert-<?= sanitize($flash['type']); ?>">
                            <?= sanitize($flash['message']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
