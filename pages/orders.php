<?php
$customersData = firebase_get('customers');
$customerOptions = [];
if ($customersData['success'] && is_array($customersData['data'])) {
    foreach ($customersData['data'] as $key => $customer) {
        $customerOptions[$key] = $customer['full_name'] ?? $key;
    }
}

$employeeData = firebase_get('employees');
$employeeOptions = [];
if ($employeeData['success'] && is_array($employeeData['data'])) {
    foreach ($employeeData['data'] as $key => $employee) {
        $employeeOptions[$key] = $employee['name'] ?? $key;
    }
}

$outletData = firebase_get('outlets');
$outletOptions = [];
if ($outletData['success'] && is_array($outletData['data'])) {
    foreach ($outletData['data'] as $key => $outlet) {
        $outletOptions[$key] = $outlet['name'] ?? $key;
    }
}

$laundryData = firebase_get('laundry_types');
$laundryOptions = [];
if ($laundryData['success'] && is_array($laundryData['data'])) {
    foreach ($laundryData['data'] as $key => $type) {
        $price = number_format((int) ($type['price_per_kg'] ?? 0), 0, ',', '.');
        $laundryOptions[$key] = ($type['name'] ?? $key) . ' - Rp ' . $price . '/Kg';
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'] ?? '';
    $employeeId = $_POST['employee_id'] ?? '';
    $outletId = $_POST['outlet_id'] ?? '';
    $laundryTypeId = $_POST['laundry_type_id'] ?? '';
    $weight = (float) ($_POST['weight'] ?? 0);
    $status = $_POST['status'] ?? 'created';
    $dueDate = $_POST['due_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($customerId === '' || !isset($customerOptions[$customerId])) {
        $errors[] = 'Pilih customer';
    }
    if ($employeeId === '' || !isset($employeeOptions[$employeeId])) {
        $errors[] = 'Pilih karyawan';
    }
    if ($outletId === '' || !isset($outletOptions[$outletId])) {
        $errors[] = 'Pilih outlet';
    }
    if ($laundryTypeId === '' || !isset($laundryData['data'][$laundryTypeId])) {
        $errors[] = 'Pilih jenis laundry';
    }
    if ($weight <= 0) {
        $errors[] = 'Berat harus lebih besar dari 0';
    }

    if (empty($errors)) {
        $pricePerKg = (int) ($laundryData['data'][$laundryTypeId]['price_per_kg'] ?? 0);
        $total = $pricePerKg * $weight;
        $timestamp = date(DATE_ATOM);
        $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid((string) microtime(true), true)), 0, 6));

        $result = firebase_push('orders', [
            'order_number' => $orderNumber,
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'outlet_id' => $outletId,
            'laundry_type_id' => $laundryTypeId,
            'weight_kg' => $weight,
            'status' => $status,
            'total_price' => $total,
            'due_date' => $dueDate,
            'notes' => $notes,
            'ordered_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        if ($result['success']) {
            set_flash('Order baru berhasil dibuat');
            header('Location: ?page=orders');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$data = firebase_get('orders');
$orders = $data['success'] && is_array($data['data']) ? $data['data'] : [];
?>
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Buat Order</h3></div>
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
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Pilih Customer --</option>
                        <?= form_select_options($customerOptions); ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Karyawan</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- Pilih Karyawan --</option>
                        <?= form_select_options($employeeOptions); ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Outlet</label>
                    <select name="outlet_id" class="form-control" required>
                        <option value="">-- Pilih Outlet --</option>
                        <?= form_select_options($outletOptions); ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Jenis Laundry</label>
                    <select name="laundry_type_id" class="form-control" required>
                        <option value="">-- Pilih Layanan --</option>
                        <?= form_select_options($laundryOptions); ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Berat (Kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="created">Created</option>
                        <option value="processing">Processing</option>
                        <option value="finished">Finished</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Jatuh Tempo</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-primary">Simpan Order</button>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header"><h3 class="card-title">Daftar Order</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No Order</th>
                    <th>Customer</th>
                    <th>Jenis Laundry</th>
                    <th>Berat</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= sanitize($order['order_number'] ?? '-'); ?></td>
                            <td><?= sanitize($customerOptions[$order['customer_id']] ?? $order['customer_id'] ?? '-'); ?></td>
                            <td><?= sanitize($laundryOptions[$order['laundry_type_id']] ?? $order['laundry_type_id'] ?? '-'); ?></td>
                            <td><?= sanitize($order['weight_kg'] ?? '0'); ?> Kg</td>
                            <td>Rp <?= number_format((int) ($order['total_price'] ?? 0), 0, ',', '.'); ?></td>
                            <td><span class="badge badge-info text-uppercase"><?= sanitize($order['status'] ?? '-'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
