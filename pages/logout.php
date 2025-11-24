<?php
require_once __DIR__ . '/../includes/bootstrap.php';

unset($_SESSION['user']);
set_flash('Anda telah keluar.');
header('Location: ?page=login');
exit;
