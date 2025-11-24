<?php
require_once __DIR__ . '/helpers.php';

function firebase_request(string $path, string $method = 'GET', ?array $data = null): array
{
    $config = app_config('firebase', []);
    $baseUrl = rtrim($config['url'] ?? '', '/');

    if ($baseUrl === '') {
        return [
            'success' => false,
            'error' => 'Konfigurasi Firebase belum diatur. Ubah file config.php untuk menambahkan URL database.'
        ];
    }

    $url = $baseUrl . '/' . ltrim($path, '/') . '.json';
    $secret = $config['secret'] ?? '';
    $hasQuery = strpos($url, '?') !== false;

    if ($secret !== '') {
        $url .= ($hasQuery ? '&' : '?') . 'auth=' . urlencode($secret);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'error' => $error ?: 'Tidak dapat terhubung ke Firebase.'
        ];
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);

    if ($statusCode >= 400) {
        return [
            'success' => false,
            'error' => $decoded['error'] ?? 'Terjadi kesalahan saat menghubungi Firebase.'
        ];
    }

    return [
        'success' => true,
        'data' => $decoded
    ];
}

function firebase_get(string $path): array
{
    return firebase_request($path);
}

function firebase_push(string $path, array $data): array
{
    return firebase_request($path, 'POST', $data);
}

function firebase_set(string $path, array $data): array
{
    return firebase_request($path, 'PUT', $data);
}

function firebase_update(string $path, array $data): array
{
    return firebase_request($path, 'PATCH', $data);
}

function firebase_delete(string $path): array
{
    return firebase_request($path, 'DELETE');
}
