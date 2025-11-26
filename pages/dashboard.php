<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$modules = [
    'customers' => 'Customers',
    'laundry_types' => 'Laundry Types',
    'outlets' => 'Outlet',
    'employees' => 'Employee',
    'orders' => 'Order',
    'payments' => 'Payment',
    'queues' => 'Queue'
];

$stats = [];
foreach ($modules as $key => $label) {
    $result = firebase_get($key);
    $count = 0;
    if ($result['success'] && is_array($result['data'])) {
        $count = count($result['data']);
    }
    $stats[] = [
        'label' => $label,
        'count' => $count,
        'key' => $key
    ];
}
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <?php foreach ($stats as $stat): ?>
        <div class="col-md-4 col-xl-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1"><?= $stat['label']; ?></h5>
                        <p class="text-muted mb-0">Total data</p>
                    </div>
                    <span class="badge badge-primary p-3 h5 mb-0"><?= $stat['count']; ?></span>
                </div>
                <div class="card-footer bg-white">
                    <a href="?page=<?= str_replace('_', '-', $stat['key']); ?>" class="text-primary">Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h3 class="card-title mb-0">Rangkuman Database Laundry</h3>
    </div>
    <div class="card-body">
        <p>Aplikasi ini mengelola entity sesuai rancangan database yang meliputi customer, jenis laundry, outlet, karyawan, order, pembayaran hingga antrian. Semua data disimpan pada Firebase Realtime Database dan dapat diakses melalui menu di sebelah kiri.</p>
    </div>
</div>
<?php
require_once __DIR__ . '/../partials/footer.php';
?>