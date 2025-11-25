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
$editingId = $_GET['edit'] ?? '';
$data = firebase_get('employees');
$employees = $data['success'] && is_array($data['data']) ? $data['data'] : [];
$editingEmployee = $editingId !== '' && isset($employees[$editingId]) ? $employees[$editingId] : null;

if ($editingId !== '' && !$editingEmployee) {
    set_flash('Karyawan tidak ditemukan.', 'danger');
    header('Location: ?page=employees');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        $result = firebase_delete('employees/' . $deleteId);

        if ($result['success']) {
            set_flash('Karyawan berhasil dihapus');
        } else {
            set_flash($result['error'] ?? 'Gagal menghapus karyawan', 'danger');
        }

        header('Location: ?page=employees');
        exit;
    }

    $editingId = $_POST['id'] ?? '';
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

    if ($editingId === '' && strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }

    if ($editingId !== '' && $password !== '' && strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }

    if ($outletId === '' || !isset($outletOptions[$outletId])) {
        $errors[] = 'Pilih outlet';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'outlet_id' => $outletId,
            'created_at' => $editingId !== '' && isset($employees[$editingId]['created_at']) ? $employees[$editingId]['created_at'] : $timestamp,
            'updated_at' => $timestamp
        ];

        if ($password !== '') {
            $payload['password'] = password_hash($password, PASSWORD_DEFAULT);
        } elseif ($editingId !== '' && isset($employees[$editingId]['password'])) {
            $payload['password'] = $employees[$editingId]['password'];
        }

        if ($editingId !== '') {
            $result = firebase_set('employees/' . $editingId, $payload);
            $message = 'Karyawan berhasil diperbarui';
        } else {
            $result = firebase_push('employees', $payload);
            $message = 'Karyawan berhasil ditambahkan';
        }

        if ($result['success']) {
            set_flash($message);
            header('Location: ?page=employees');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$formData = $editingEmployee ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = array_merge($formData, [
        'name' => $name ?? ($formData['name'] ?? ''),
        'email' => $email ?? ($formData['email'] ?? ''),
        'phone' => $phone ?? ($formData['phone'] ?? ''),
        'role' => $role ?? ($formData['role'] ?? ''),
        'outlet_id' => $outletId ?? ($formData['outlet_id'] ?? ''),
    ]);
}
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title"><?= $editingId !== '' ? 'Edit Karyawan' : 'Tambah Karyawan'; ?></h3></div>
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
                    <?php if ($editingId !== ''): ?>
                        <input type="hidden" name="id" value="<?= sanitize($editingId); ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($formData['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= sanitize($formData['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= sanitize($formData['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" <?= (isset($formData['role']) && $formData['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="kasir" <?= (isset($formData['role']) && $formData['role'] === 'kasir') ? 'selected' : ''; ?>>Kasir</option>
                            <option value="staff" <?= (isset($formData['role']) && $formData['role'] === 'staff') ? 'selected' : ''; ?>>Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password <?= $editingId !== '' ? '(kosongkan jika tidak diubah)' : ''; ?></label>
                        <input type="password" name="password" class="form-control" minlength="6" <?= $editingId === '' ? 'required' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Outlet</label>
                        <select name="outlet_id" class="form-control" required>
                            <option value="">-- Pilih Outlet --</option>
                            <?= form_select_options($outletOptions, $formData['outlet_id'] ?? null); ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary">Simpan</button>
                    <?php if ($editingId !== ''): ?>
                        <a href="?page=employees" class="btn btn-secondary ml-2">Batal</a>
                    <?php endif; ?>
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
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employeeId => $employee): ?>
                                <tr>
                                    <td><?= sanitize($employee['name'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['email'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['role'] ?? '-'); ?></td>
                                    <td><?= sanitize($outletOptions[$employee['outlet_id']] ?? $employee['outlet_id'] ?? '-'); ?></td>
                                    <td><?= sanitize($employee['phone'] ?? '-'); ?></td>
                                    <td class="text-right">
                                        <a href="?page=employees&edit=<?= sanitize($employeeId); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus karyawan ini?');">
                                            <input type="hidden" name="delete_id" value="<?= sanitize($employeeId); ?>">
                                            <button class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../partials/footer.php';
?>