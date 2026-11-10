<?php
require_once __DIR__ . '/config/app.php';
if (is_logged_in()) {
    redirect_to('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (hash_equals(ADMIN_USERNAME, $username) && hash_equals(ADMIN_PASSWORD, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        redirect_to('index.php');
    }
    $error = 'Incorrect username or password.';
}
$pageTitle = 'Local Admin Login';
include __DIR__ . '/includes/header.php';
?>
<section class="login-card">
  <span class="eyebrow">LOCAL ADMIN ACCESS</span>
  <h1>Welcome back</h1>
  <p>Sign in to manage laboratory equipment borrowing records.</p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="username">Username</label>
    <input id="username" name="username" autocomplete="username" required autofocus>
    <label for="password">Password</label>
    <input id="password" type="password" name="password" autocomplete="current-password" required>
    <button type="submit" class="button primary full">Login</button>
  </form>
  <p class="muted small">Local practice credentials are in the README.</p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
