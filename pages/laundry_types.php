<?php
require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
authorize(['admin']);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $result = firebase_push('laundry_types', [
            'name' => $name,
            'price_per_kg' => $price,
            'description' => $description,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        if ($result['success']) {
            set_flash('Jenis laundry berhasil ditambahkan');
            header('Location: ?page=laundry-types');
            exit;
        }

        $errors[] = $result['error'] ?? 'Gagal menyimpan data';
    }
}

$data = firebase_get('laundry_types');
$laundryTypes = $data['success'] && is_array($data['data']) ? $data['data'] : [];
require_once __DIR__ . '/../partials/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Tambah Jenis Laundry</h3></div>
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
                        <label>Nama Layanan</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga / Kg</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
            <div class="card-header"><h3 class="card-title">Daftar Jenis Laundry</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga / Kg</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laundryTypes)): ?>
                            <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($laundryTypes as $type): ?>
                                <tr>
                                    <td><?= sanitize($type['name'] ?? '-'); ?></td>
                                    <td>Rp <?= number_format((int) ($type['price_per_kg'] ?? 0), 0, ',', '.'); ?></td>
                                    <td><?= sanitize($type['description'] ?? '-'); ?></td>
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