<?php
require_once __DIR__ . '/../config/app.php';
require_login();
$slug = (string) ($_GET['module'] ?? '');
$module = get_module($slug);
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$id = $id ?: 0;
$editing = $id > 0;
$record = $editing ? find_record($pdo, $module, $id) : [];
if ($editing && !$record) {
    http_response_code(404);
    exit('Record not found.');
}

$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_old']);
if (($old['module'] ?? '') !== $slug) {
    $old = [];
}
$pageTitle = ($editing ? 'Edit ' : 'Add ') . $module['singular'];
include __DIR__ . '/../includes/header.php';
?>
<section class="page-header">
  <div>
    <span class="eyebrow"><?= $editing ? 'UPDATE RECORD' : 'ADD NEW RECORD' ?></span>
    <h1><?= e($pageTitle) ?></h1>
    <p class="muted">Fields marked * are required.</p>
  </div>
  <a class="button light" href="<?= e(app_url('crud/index.php?module=' . $slug)) ?>">Back to List</a>
</section>
<section class="panel">
  <?php if ($slug === 'borrowings'): ?>
    <div class="hint">Choose an existing student, equipment item, and staff member. If a dropdown is empty, add records to that module first.</div>
  <?php endif; ?>
  <form method="post" action="<?= e(app_url('crud/save.php')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="module" value="<?= e($slug) ?>">
    <input type="hidden" name="id" value="<?= e($id) ?>">
    <div class="form-grid">
    <?php foreach ($module['fields'] as $name => $field):
      $required = $field['required'] ?? true;
      $value = $old[$name] ?? ($record[$name] ?? '');
      if (!$editing && !$old && $name === 'borrowDate') {
          $value = date('Y-m-d\TH:i');
      }
      if ($field['type'] === 'datetime-local' && $value !== '') {
          $value = str_replace(' ', 'T', substr((string) $value, 0, 16));
      }
    ?>
      <div class="form-field">
        <label for="<?= e($name) ?>"><?= e($field['label']) ?>
          <?php if ($required): ?><span class="required">*</span><?php endif; ?>
        </label>
        <?php if ($field['type'] === 'select'): ?>
          <select id="<?= e($name) ?>" name="<?= e($name) ?>" <?= $required ? 'required' : '' ?>>
            <option value="">-- Select <?= e($field['label']) ?> --</option>
            <?php foreach ($pdo->query($field['sql']) as $option): ?>
              <option value="<?= e($option['id']) ?>" <?= (string) $value === (string) $option['id'] ? 'selected' : '' ?>>
                <?= e($option['label']) ?> (#<?= e($option['id']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        <?php elseif ($field['type'] === 'textarea'): ?>
          <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" rows="3"><?= e($value) ?></textarea>
        <?php else: ?>
          <input id="<?= e($name) ?>" name="<?= e($name) ?>"
            type="<?= e($field['type']) ?>" value="<?= e($value) ?>"
            <?= $required ? 'required' : '' ?>
            <?php if (isset($field['max']) && $field['type'] === 'text'): ?>maxlength="<?= e($field['max']) ?>"<?php endif; ?>
            <?php if (isset($field['min'])): ?>min="<?= e($field['min']) ?>"<?php endif; ?>
            <?php if (isset($field['max']) && $field['type'] === 'number'): ?>max="<?= e($field['max']) ?>"<?php endif; ?>>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
    <div class="form-actions">
      <button type="submit" class="button primary"><?= $editing ? 'Save Changes' : 'Save Record' ?></button>
      <a class="button light" href="<?= e(app_url('crud/index.php?module=' . $slug)) ?>">Cancel</a>
    </div>
  </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
