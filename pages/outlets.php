<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin']);

$errors = [];
$editingId = $_GET['edit'] ?? '';
$data = firebase_get('outlets');
$outlets = $data['success'] && is_array($data['data']) ? $data['data'] : [];
$editingOutlet = $editingId !== '' && isset($outlets[$editingId]) ? $outlets[$editingId] : null;

if ($editingId !== '' && !$editingOutlet) {
    set_flash('Outlet tidak ditemukan.', 'danger');
    header('Location: ?page=outlets');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        $result = firebase_delete('outlets/' . $deleteId);

        if ($result['success']) {
            set_flash('Outlet berhasil dihapus');
        } else {
            set_flash($result['error'] ?? 'Gagal menghapus outlet', 'danger');
        }

        header('Location: ?page=outlets');
        exit;
    }

    $editingId = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') {
        $errors[] = 'Nama outlet wajib diisi';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $payload = [
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'created_at' => $editingId !== '' && isset($outlets[$editingId]['created_at']) ? $outlets[$editingId]['created_at'] : $timestamp,
            'updated_at' => $timestamp
        ];

        if ($editingId !== '') {
            $result = firebase_set('outlets/' . $editingId, $payload);
            $message = 'Outlet berhasil diperbarui';
        } else {
            $result = firebase_push('outlets', $payload);
            $message = 'Outlet berhasil ditambahkan';
        }

        if ($result['success']) {
            set_flash($message);
            header('Location: ?page=outlets');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$formData = $editingOutlet ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = array_merge($formData, [
        'name' => $name ?? ($formData['name'] ?? ''),
        'phone' => $phone ?? ($formData['phone'] ?? ''),
        'address' => $address ?? ($formData['address'] ?? '')
    ]);
}
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-header bg-primary text-white"><h3 class="card-title mb-0"><?= $editingId !== '' ? 'Edit Outlet' : 'Tambah Outlet'; ?></h3></div>
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
                        <label>Nama Outlet</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($formData['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= sanitize($formData['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control" rows="3"><?= sanitize($formData['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="card-footer text-right bg-white">
                    <button class="btn btn-primary">Simpan</button>
                    <?php if ($editingId !== ''): ?>
                        <a href="?page=outlets" class="btn btn-secondary ml-2">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h3 class="card-title mb-0">Daftar Outlet</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($outlets)): ?>
                            <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($outlets as $outletId => $outlet): ?>
                                <tr>
                                    <td><?= sanitize($outlet['name'] ?? '-'); ?></td>
                                    <td><?= sanitize($outlet['phone'] ?? '-'); ?></td>
                                    <td><?= sanitize($outlet['address'] ?? '-'); ?></td>
                                    <td class="text-right">
                                        <a href="?page=outlets&edit=<?= sanitize($outletId); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus outlet ini?');">
                                            <input type="hidden" name="delete_id" value="<?= sanitize($outletId); ?>">
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
