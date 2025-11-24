<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function app_config(?string $key = null, $default = null)
{
    static $config;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flashes'][] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flashes(): array
{
    $messages = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);

    return $messages;
}

function sanitize(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function form_select_options(array $options, ?string $selected = null): string
{
    $html = '';
    foreach ($options as $value => $label) {
        $isSelected = $selected !== null && (string) $value === (string) $selected;
        $html .= sprintf('<option value="%s" %s>%s</option>', sanitize((string) $value), $isSelected ? 'selected' : '', sanitize($label));
    }

    return $html;
}
