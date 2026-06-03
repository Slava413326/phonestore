<?php // admin/partials/product_form.php
// $p is available when editing, otherwise empty
$fp = $p ?? [];
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
  <div class="form-group">
    <label class="form-label">Название</label>
    <input class="form-input" type="text" name="name" required value="<?= htmlspecialchars($fp['name'] ?? '') ?>">
  </div>
  <div class="form-group">
    <label class="form-label">Бренд</label>
    <select class="form-input" name="brand" required>
      <?php foreach(['Apple','Samsung','Xiaomi','Huawei','Other'] as $b): ?>
        <option value="<?= $b ?>" <?= ($fp['brand'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label class="form-label">Цена (руб.)</label>
    <input class="form-input" type="number" name="price" required min="1" value="<?= $fp['price'] ?? '' ?>">
  </div>
  <div class="form-group">
    <label class="form-label">Остаток на складе</label>
    <input class="form-input" type="number" name="stock" min="0" value="<?= $fp['stock'] ?? 100 ?>">
  </div>
  <div class="form-group" style="grid-column:1/-1">
    <label class="form-label">URL изображения</label>
    <input class="form-input" type="text" name="image" placeholder="https://..." value="<?= htmlspecialchars($fp['image'] ?? '') ?>">
  </div>
  <div class="form-group" style="grid-column:1/-1">
    <label class="form-label">Описание</label>
    <textarea class="form-input" name="description" rows="2" style="resize:vertical"><?= htmlspecialchars($fp['description'] ?? '') ?></textarea>
  </div>
</div>
