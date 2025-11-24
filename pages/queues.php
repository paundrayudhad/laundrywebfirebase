<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin', 'kasir', 'staff']);

$orderData = firebase_get('orders');
$orderOptions = [];
if ($orderData['success'] && is_array($orderData['data'])) {
    foreach ($orderData['data'] as $key => $order) {
        $orderOptions[$key] = $order['order_number'] ?? $key;
    }
}

$currentQueue = firebase_get('queues');
$queueItems = $currentQueue['success'] && is_array($currentQueue['data']) ? $currentQueue['data'] : [];
$nextNumber = count($queueItems) + 1;

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? '';
    $position = (int) ($_POST['position'] ?? $nextNumber);
    $status = $_POST['status'] ?? 'waiting';

    if ($orderId === '' || !isset($orderOptions[$orderId])) {
        $errors[] = 'Pilih order';
    }
    if ($position <= 0) {
        $errors[] = 'Nomor antrian tidak valid';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $result = firebase_push('queues', [
            'order_id' => $orderId,
            'position' => $position,
            'status' => $status,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        if ($result['success']) {
            set_flash('Antrian berhasil ditambahkan');
            header('Location: ?page=queues');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}
?>
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Tambah Antrian</h3></div>
    <form method="POST">
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= sanitize($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Order</label>
                <select name="order_id" class="form-control" required>
                    <option value="">-- Pilih Order --</option>
                    <?= form_select_options($orderOptions); ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nomor Antrian</label>
                    <input type="number" name="position" class="form-control" value="<?= $nextNumber; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="waiting">Waiting</option>
                        <option value="processing">Processing</option>
                        <option value="done">Done</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header"><h3 class="card-title">Daftar Antrian</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Order</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($queueItems)): ?>
                    <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($queueItems as $item): ?>
                        <tr>
                            <td><?= sanitize((string) ($item['position'] ?? '-')); ?></td>
                            <td><?= sanitize($orderOptions[$item['order_id']] ?? $item['order_id'] ?? '-'); ?></td>
                            <td><span class="badge badge-warning text-uppercase"><?= sanitize($item['status'] ?? '-'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
