<?php
require_once __DIR__ . '/includes/bootstrap.php';

$dummyData = require __DIR__ . '/data/dummy_data.php';

$results = [];
foreach ($dummyData as $path => $records) {
    $results[$path] = firebase_set($path, $records);
}

header('Content-Type: text/plain; charset=UTF-8');
echo "Seeding dummy data ke Firebase...\n\n";
foreach ($results as $path => $result) {
    if ($result['success']) {
        echo "[OK] {$path}\n";
    } else {
        echo "[GAGAL] {$path} -> " . ($result['error'] ?? 'Unknown error') . "\n";
    }
}

if (isset($_SERVER['HTTP_HOST'])) {
    echo "\nCatatan: jalankan file ini hanya sekali untuk mengisi data awal.\n";
}
