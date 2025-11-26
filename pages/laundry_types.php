<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin']);

$errors = [];
$editingId = $_GET['edit'] ?? '';
$data = firebase_get('laundry_types');
$laundryTypes = $data['success'] && is_array($data['data']) ? $data['data'] : [];
$editingType = $editingId !== '' && isset($laundryTypes[$editingId]) ? $laundryTypes[$editingId] : null;

if ($editingId !== '' && !$editingType) {
    set_flash('Jenis laundry tidak ditemukan.', 'danger');
    header('Location: ?page=laundry-types');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        $result = firebase_delete('laundry_types/' . $deleteId);

        if ($result['success']) {
            set_flash('Jenis laundry berhasil dihapus');
        } else {
            set_flash($result['error'] ?? 'Gagal menghapus jenis laundry', 'danger');
        }

        header('Location: ?page=laundry-types');
        exit;
    }

    $editingId = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $price = (int) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = 'Nama layanan wajib diisi';
    }

    if ($price <= 0) {
        $errors[] = 'Harga harus lebih besar dari 0';
    }

    if (empty($errors)) {
        $timestamp = date(DATE_ATOM);
        $payload = [
            'name' => $name,
            'price_per_kg' => $price,
            'description' => $description,
            'created_at' => $editingId !== '' && isset($laundryTypes[$editingId]['created_at']) ? $laundryTypes[$editingId]['created_at'] : $timestamp,
            'updated_at' => $timestamp
        ];

        if ($editingId !== '') {
            $result = firebase_set('laundry_types/' . $editingId, $payload);
            $message = 'Jenis laundry berhasil diperbarui';
        } else {
            $result = firebase_push('laundry_types', $payload);
            $message = 'Jenis laundry berhasil ditambahkan';
        }

        if ($result['success']) {
            set_flash($message);
            header('Location: ?page=laundry-types');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$formData = $editingType ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = array_merge($formData, [
        'name' => $name ?? ($formData['name'] ?? ''),
        'price_per_kg' => $price ?? ($formData['price_per_kg'] ?? ''),
        'description' => $description ?? ($formData['description'] ?? ''),
    ]);
}
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-header bg-primary text-white"><h3 class="card-title mb-0"><?= $editingId !== '' ? 'Edit Jenis Laundry' : 'Tambah Jenis Laundry'; ?></h3></div>
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
                        <label>Nama Layanan</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($formData['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Harga / Kg</label>
                        <input type="number" name="price" class="form-control" value="<?= sanitize((string) ($formData['price_per_kg'] ?? '')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"><?= sanitize($formData['description'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="card-footer text-right bg-white">
                    <button class="btn btn-primary">Simpan</button>
                    <?php if ($editingId !== ''): ?>
                        <a href="?page=laundry-types" class="btn btn-secondary ml-2">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h3 class="card-title mb-0">Daftar Jenis Laundry</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga / Kg</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laundryTypes)): ?>
                            <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($laundryTypes as $typeId => $type): ?>
                                <tr>
                                    <td><?= sanitize($type['name'] ?? '-'); ?></td>
                                    <td>Rp <?= number_format((int) ($type['price_per_kg'] ?? 0), 0, ',', '.'); ?></td>
                                    <td><?= sanitize($type['description'] ?? '-'); ?></td>
                                    <td class="text-right">
                                        <a href="?page=laundry-types&edit=<?= sanitize($typeId); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus jenis laundry ini?');">
                                            <input type="hidden" name="delete_id" value="<?= sanitize($typeId); ?>">
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