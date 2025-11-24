<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    header('Location: ?page=dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $response = firebase_get('employees');
    if ($response['success']) {
        $employees = $response['data'] ?? [];
        foreach ($employees as $id => $employee) {
            $employeeEmail = strtolower($employee['email'] ?? '');
            if ($employeeEmail === strtolower($email) && isset($employee['password']) && password_verify($password, $employee['password'])) {
                $_SESSION['user'] = [
                    'id' => $id,
                    'name' => $employee['name'] ?? 'Pengguna',
                    'email' => $employee['email'] ?? $email,
                    'role' => $employee['role'] ?? 'staff'
                ];
                set_flash('Berhasil masuk sebagai ' . sanitize($_SESSION['user']['name']) . '.');
                header('Location: ?page=dashboard');
                exit;
            }
        }
        $error = 'Email atau password tidak valid.';
    } else {
        $error = $response['error'] ?? 'Tidak dapat memuat data pengguna.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= sanitize(app_config('app')['name'] ?? 'Laundry App'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Laundry</b>Firebase
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masuk untuk memulai sesi Anda</p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= sanitize($error); ?></div>
            <?php endif; ?>
            <?php foreach (get_flashes() as $flash): ?>
                <div class="alert alert-<?= sanitize($flash['type']); ?>"><?= sanitize($flash['message']); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
