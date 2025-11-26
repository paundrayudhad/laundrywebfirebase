<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

authorize(['admin', 'kasir']);

$errors = [];
$editingId = $_GET['edit'] ?? '';
$data = firebase_get('customers');
$customers = $data['success'] && is_array($data['data']) ? $data['data'] : [];
$editingCustomer = $editingId !== '' && isset($customers[$editingId]) ? $customers[$editingId] : null;

if ($editingId !== '' && !$editingCustomer) {
    set_flash('Customer tidak ditemukan.', 'danger');
    header('Location: ?page=customers');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        $result = firebase_delete('customers/' . $deleteId);

        if ($result['success']) {
            set_flash('Customer berhasil dihapus');
        } else {
            set_flash($result['error'] ?? 'Gagal menghapus customer', 'danger');
        }

        header('Location: ?page=customers');
        exit;
    }

    $editingId = $_POST['id'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($fullName === '') {
        $errors[] = 'Nama lengkap wajib diisi';
    }

    if ($phone === '') {
        $errors[] = 'Nomor telepon wajib diisi';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $payload = [
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'created_at' => $editingId !== '' && isset($customers[$editingId]['created_at']) ? $customers[$editingId]['created_at'] : $timestamp,
            'updated_at' => $timestamp
        ];

        if ($editingId !== '') {
            $result = firebase_set('customers/' . $editingId, $payload);
            $message = 'Customer berhasil diperbarui';
        } else {
            $result = firebase_push('customers', $payload);
            $message = 'Customer berhasil ditambahkan';
        }

        if ($result['success']) {
            set_flash($message);
            header('Location: ?page=customers');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$formData = $editingCustomer ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = array_merge($formData, [
        'full_name' => $fullName ?? ($formData['full_name'] ?? ''),
        'phone' => $phone ?? ($formData['phone'] ?? ''),
        'email' => $email ?? ($formData['email'] ?? ''),
        'address' => $address ?? ($formData['address'] ?? ''),
    ]);
}
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-header bg-primary text-white"><h3 class="card-title mb-0"><?= $editingId !== '' ? 'Edit Customer' : 'Tambah Customer'; ?></h3></div>
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
                        <label>Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control" value="<?= sanitize($formData['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= sanitize($formData['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= sanitize($formData['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control" rows="3"><?= sanitize($formData['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="card-footer text-right bg-white">
                    <button class="btn btn-primary">Simpan</button>
                    <?php if ($editingId !== ''): ?>
                        <a href="?page=customers" class="btn btn-secondary ml-2">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h3 class="card-title mb-0">Daftar Customer</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customerId => $customer): ?>
                                <tr>
                                    <td><?= sanitize($customer['full_name'] ?? '-'); ?></td>
                                    <td><?= sanitize($customer['phone'] ?? '-'); ?></td>
                                    <td><?= sanitize($customer['email'] ?? '-'); ?></td>
                                    <td><?= sanitize($customer['address'] ?? '-'); ?></td>
                                    <td class="text-right">
                                        <a href="?page=customers&edit=<?= sanitize($customerId); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus customer ini?');">
                                            <input type="hidden" name="delete_id" value="<?= sanitize($customerId); ?>">
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
