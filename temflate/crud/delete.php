<?php
require_once __DIR__ . '/../config/app.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the Delete button on the list page.');
}
verify_csrf();
$slug = (string) ($_POST['module'] ?? '');
$module = get_module($slug);
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('error', 'Invalid record ID.');
    redirect_to('crud/index.php?module=' . $slug);
}

try {
    $stmt = $pdo->prepare('DELETE FROM ' . $module['table'] . ' WHERE ' . $module['pk'] . ' = ?');
    $stmt->execute([$id]);
    flash($stmt->rowCount() ? 'success' : 'error',
        $stmt->rowCount() ? 'Record deleted successfully.' : 'Record not found.');
} catch (PDOException $error) {
    if ($error->getCode() === '23000') {
        flash('error', 'Cannot delete: this record is used in a borrowing transaction. Delete or change related borrowings first.');
    } else {
        flash('error', 'Database error while deleting. Check your database connection and values.');
    }
}
redirect_to('crud/index.php?module=' . $slug);
