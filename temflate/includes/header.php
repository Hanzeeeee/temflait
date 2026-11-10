<?php
// Expected variables: $pageTitle; config/app.php must be loaded first.
$notice = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Laboratory Borrowing System') ?></title>
  <link rel="stylesheet" href="<?= e(app_url('assets/style.css')) ?>">
</head>
<body>
<?php if (is_logged_in() && !($hideNavigation ?? false)): ?>
<nav class="plain-topbar" aria-label="Navigation">
  <a class="button light" href="<?= e(app_url('index.php')) ?>">Home</a>
  <form method="post" action="<?= e(app_url('logout.php')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <button class="logout-button" type="submit">Logout</button>
  </form>
</nav>
<?php endif; ?>
<main class="container <?= ($hideNavigation ?? false) ? 'dashboard-container' : '' ?>">
  <?php if ($notice): ?>
    <div class="alert <?= $notice['type'] === 'success' ? 'alert-success' : 'alert-error' ?>" role="status">
      <?= e($notice['message']) ?>
    </div>
  <?php endif; ?>
