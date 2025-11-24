<?php
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
?>
<div class="row">
    <?php foreach ($stats as $stat): ?>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $stat['count']; ?></h3>
                    <p><?= $stat['label']; ?></p>
                </div>
                <div class="icon"><i class="fas fa-database"></i></div>
                <a href="?page=<?= str_replace('_', '-', $stat['key']); ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="card">
    <div class="card-header border-0">
        <h3 class="card-title">Rangkuman Database Laundry</h3>
    </div>
    <div class="card-body">
        <p>Aplikasi ini mengelola entity sesuai rancangan database yang meliputi customer, jenis laundry, outlet, karyawan, order, pembayaran hingga antrian. Semua data disimpan pada Firebase Realtime Database dan dapat diakses melalui menu di sebelah kiri.</p>
    </div>
</div>
