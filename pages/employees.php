<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin']);

$outletData = firebase_get('outlets');
$outletOptions = [];
if ($outletData['success'] && is_array($outletData['data'])) {
    foreach ($outletData['data'] as $key => $outlet) {
        $outletOptions[$key] = $outlet['name'] ?? $key;
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $outletId = $_POST['outlet_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($name === '') {
        $errors[] = 'Nama karyawan wajib diisi';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }

    if ($outletId === '' || !isset($outletOptions[$outletId])) {
        $errors[] = 'Pilih outlet';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $result = firebase_push('employees', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'outlet_id' => $outletId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        if ($result['success']) {
            set_flash('Karyawan berhasil ditambahkan');
            header('Location: ?page=employees');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$data = firebase_get('employees');
$employees = $data['success'] && is_array($data['data']) ? $data['data'] : [];
?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Tambah Karyawan</h3></div>
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
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>Outlet</label>
                        <select name="outlet_id" class="form-control" required>
                            <option value="">-- Pilih Outlet --</option>
                            <?= form_select_options($outletOptions); ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Karyawan</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Outlet</th>
                            <th>Telepon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?= sanitize($employee['name'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['email'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['role'] ?? '-'); ?></td>
                                    <td><?= sanitize($outletOptions[$employee['outlet_id']] ?? $employee['outlet_id'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['phone'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
