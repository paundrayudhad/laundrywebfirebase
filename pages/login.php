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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <h1 class="h3 mb-0 font-weight-bold">Laundry Firebase</h1>
                <p class="text-muted">Silakan masuk untuk melanjutkan</p>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= sanitize($error); ?></div>
                    <?php endif; ?>
                    <?php foreach (get_flashes() as $flash): ?>
                        <div class="alert alert-<?= sanitize($flash['type']); ?>"><?= sanitize($flash['message']); ?></div>
                    <?php endforeach; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" id="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
