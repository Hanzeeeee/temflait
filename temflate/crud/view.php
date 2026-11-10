<?php
require_once __DIR__ . '/../config/app.php';
require_login();
$slug = (string) ($_GET['module'] ?? '');
$module = get_module($slug);
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$record = $id ? find_record($pdo, $module, (int) $id) : false;
if (!$record) {
    http_response_code(404);
    exit('Record not found.');
}
$maps = lookup_maps($pdo, $module);
$pageTitle = 'View ' . $module['singular'];
include __DIR__ . '/../includes/header.php';
?>
<section class="page-header">
  <div><span class="eyebrow">RECORD DETAILS</span><h1><?= e($module['singular']) ?> #<?= e($id) ?></h1></div>
  <div class="actions">
    <a class="button primary" href="<?= e(app_url('crud/form.php?module=' . $slug . '&id=' . $id)) ?>">Edit Record</a>
    <a class="button light" href="<?= e(app_url('crud/index.php?module=' . $slug)) ?>">Back to List</a>
  </div>
</section>
<section class="panel">
  <dl class="detail-list">
    <div class="detail-row"><dt><?= e($module['pk']) ?></dt><dd><?= e($id) ?></dd></div>
    <?php foreach ($module['fields'] as $name => $field): ?>
      <div class="detail-row">
        <dt><?= e($field['label']) ?></dt>
        <dd><?= display_field($name, $record[$name], $maps) ?></dd>
      </div>
    <?php endforeach; ?>
  </dl>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
