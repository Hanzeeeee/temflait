<?php
require_once __DIR__ . '/../config/app.php';
require_login();
$slug = (string) ($_GET['module'] ?? '');
$module = get_module($slug);
$search = trim((string) ($_GET['search'] ?? ''));
$search = substr($search, 0, 100);

// Module table/column names come only from config/modules.php.
$sql = 'SELECT * FROM ' . $module['table'];
$params = [];
if ($search !== '') {
    $clauses = [];
    foreach ($module['search'] as $column) {
        $clauses[] = 'CAST(' . $column . ' AS CHAR) LIKE ?';
        $params[] = '%' . $search . '%';
    }
    $sql .= ' WHERE ' . implode(' OR ', $clauses);
}
$sql .= ' ORDER BY ' . $module['pk'] . ' DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$maps = lookup_maps($pdo, $module);
$pageTitle = $module['label'] . ' Management';
include __DIR__ . '/../includes/header.php';
?>
<section class="page-header">
  <div>
    <span class="eyebrow">MANAGEMENT MODULE</span>
    <h1><?= e($module['label']) ?></h1>
    <p class="muted">Add, search, update, delete, and view <?= e(strtolower($module['label'])) ?> records.</p>
  </div>
  <a class="button primary" href="<?= e(app_url('crud/form.php?module=' . $slug)) ?>">+ Add <?= e($module['singular']) ?></a>
</section>
<section class="panel">
  <form method="get" class="search-bar">
    <input type="hidden" name="module" value="<?= e($slug) ?>">
    <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search records by ID or details...">
    <button type="submit" class="button primary">Search</button>
    <?php if ($search !== ''): ?>
      <a class="button light" href="<?= e(app_url('crud/index.php?module=' . $slug)) ?>">Clear</a>
    <?php endif; ?>
  </form>
  <p class="muted small"><?= count($rows) ?> record(s) displayed</p>
  <div class="table-wrap">
  <table>
    <thead><tr>
      <th><?= e($module['pk']) ?></th>
      <?php foreach ($module['fields'] as $name => $field): ?>
        <th><?= e($field['label']) ?></th>
      <?php endforeach; ?>
      <th>Actions</th>
    </tr></thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td class="empty" colspan="<?= count($module['fields']) + 2 ?>">No records found. Try adding one or changing your search.</td></tr>
    <?php else: ?>
      <?php foreach ($rows as $row): ?>
      <tr>
        <td><?= e($row[$module['pk']]) ?></td>
        <?php foreach ($module['fields'] as $name => $field): ?>
          <td><?= display_field($name, $row[$name], $maps) ?></td>
        <?php endforeach; ?>
        <td>
          <div class="table-actions">
            <a class="button outline small-button" href="<?= e(app_url('crud/view.php?module=' . $slug . '&id=' . $row[$module['pk']])) ?>">View</a>
            <a class="button light small-button" href="<?= e(app_url('crud/form.php?module=' . $slug . '&id=' . $row[$module['pk']])) ?>">Edit</a>
            <form method="post" action="<?= e(app_url('crud/delete.php')) ?>" onsubmit="return confirm('Delete this record? Referenced master records cannot be deleted until their borrowings are removed.')">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="module" value="<?= e($slug) ?>">
              <input type="hidden" name="id" value="<?= e($row[$module['pk']]) ?>">
              <button type="submit" class="button danger small-button">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
