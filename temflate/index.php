<?php
require_once __DIR__ . '/config/app.php';
require_login();
$hideNavigation = true;
$pageTitle = 'Dashboard - CLINIC';
include __DIR__ . '/includes/header.php';
?>
<div class="dashboard-header">
  <h1>Welcome to the Clinic System</h1>
  <form method="post" action="<?= e(app_url('logout.php')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <button class="logout-button" type="submit">Logout</button>
  </form>
</div>
<p>Select a module to manage:</p>

<div class="home-grid">
  <?php
  $descriptions = [
      'doctor' => 'Store and manage doctor information.',
      'patient' => 'Manage patient information.',
      'consultation' => 'Manage consultation information.',
  ];
  $buttonColors = [
      'doctor' => 'blue',
      'patient' => 'green',
      'consultation' => 'yellow',
  ];
  foreach (MODULES as $slug => $module):
  ?>
    <div class="home-card">
      <h3><?= e($module['label']) ?></h3>
      <p><?= e($descriptions[$slug] ?? 'Manage records.') ?></p>
      <a class="button <?= e($buttonColors[$slug] ?? 'blue') ?>"
         href="<?= e(app_url('crud/index.php?module=' . $slug)) ?>">
        Manage <?= e($module['label']) ?>
      </a>
    </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
