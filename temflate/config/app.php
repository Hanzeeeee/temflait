<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/database.php';

// Automatically detect the URL of this project within XAMPP htdocs.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
if (in_array(basename($scriptDir), ['crud', 'inquiries'], true)) {
    $scriptDir = str_replace('\\', '/', dirname($scriptDir));
}
define('APP_BASE_URL', ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/'));

function app_url(string $path = ''): string
{
    return APP_BASE_URL . '/' . ltrim($path, '/');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect_to(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        http_response_code(403);
        exit('Invalid form token. Refresh the page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $value;
}

function find_record(PDO $pdo, array $module, int $id): array|false
{
    $sql = 'SELECT * FROM ' . $module['table'] . ' WHERE ' . $module['pk'] . ' = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function lookup_maps(PDO $pdo, array $module): array
{
    $maps = [];
    foreach ($module['fields'] as $name => $field) {
        if ($field['type'] === 'select') {
            $maps[$name] = [];
            foreach ($pdo->query($field['sql']) as $option) {
                $maps[$name][(string) $option['id']] = $option['label'];
            }
        }
    }
    return $maps;
}

function display_field(string $field, mixed $value, array $maps): string
{
    if ($value === null || $value === '') {
        return 'Not yet recorded';
    }
    if (isset($maps[$field])) {
        return e(($maps[$field][(string) $value] ?? 'Unknown') . ' (#' . $value . ')');
    }
    return e($value);
}

function valid_date(string $value, string $format): bool
{
    $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
    return $date !== false && $date->format($format) === $value;
}
