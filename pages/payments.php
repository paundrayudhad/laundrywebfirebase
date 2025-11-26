<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin', 'kasir']);

$orderData = firebase_get('orders');
$orderOptions = [];
if ($orderData['success'] && is_array($orderData['data'])) {
    foreach ($orderData['data'] as $key => $order) {
        $orderOptions[$key] = ($order['order_number'] ?? $key) . ' - Rp ' . number_format((int) ($order['total_price'] ?? 0), 0, ',', '.');
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? '';
    $amount = (int) ($_POST['amount'] ?? 0);
    $method = trim($_POST['method'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $notes = trim($_POST['notes'] ?? '');

    if ($orderId === '' || !isset($orderOptions[$orderId])) {
        $errors[] = 'Pilih order';
    }
    if ($amount <= 0) {
        $errors[] = 'Nominal pembayaran tidak valid';
    }
    if ($method === '') {
        $errors[] = 'Metode pembayaran wajib diisi';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $result = firebase_push('payments', [
            'order_id' => $orderId,
            'amount' => $amount,
            'method' => $method,
            'status' => $status,
            'notes' => $notes,
            'paid_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        if ($result['success']) {
            set_flash('Pembayaran berhasil dicatat');
            header('Location: ?page=payments');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$data = firebase_get('payments');
$payments = $data['success'] && is_array($data['data']) ? $data['data'] : [];
require_once __DIR__ . '/../partials/header.php';
?>
<div class="card border-primary shadow-sm">
    <div class="card-header bg-primary text-white"><h3 class="card-title mb-0">Tambah Pembayaran</h3></div>
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
                    <label>Nominal</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Metode</label>
                    <input type="text" name="method" class="form-control" placeholder="Cash / Transfer" required>
                </div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="card-footer text-right bg-white">
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-white"><h3 class="card-title mb-0">Riwayat Pembayaran</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= sanitize($orderOptions[$payment['order_id']] ?? $payment['order_id'] ?? '-'); ?></td>
                            <td>Rp <?= number_format((int) ($payment['amount'] ?? 0), 0, ',', '.'); ?></td>
                            <td><?= sanitize($payment['method'] ?? '-'); ?></td>
                            <td><span class="badge badge-success text-uppercase"><?= sanitize($payment['status'] ?? '-'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
require_once __DIR__ . '/../partials/footer.php';
?>
