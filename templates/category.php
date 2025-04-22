<div class="main">
  <form method="POST" action="/category/<?= $category['id'] > 0 ? $category['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $category['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $category['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="parent">Parent category</label>
    <?php if (count($categories) > 0): ?>
    <?php if ($category['id'] > 0): ?>
    <input type="hidden" name="parent_id" value="<?= $parent_id ?>"/>
    <?php endif; ?>
    <select name="parent_id" id="parent"<?= isset($errors['parent']) ? ' class="error"' : "" ?> <?= $category['id'] > 0 ? "disabled" : "" ?>>
      <option value="0">Select category</option>
      <?php foreach ($categories as $parent_category): ?>
      <option value="<?= $parent_category['id'] ?>" <?= $parent_category['id'] == $parent_id ? "selected" : "" ?>>
        <?= $parent_category['depth'] > 0 ? str_repeat("&emsp;", $parent_category['depth']) : "" ?>
        <?= $parent_category['name'] ?>
      </option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="parent_id" disabled><option>No categories found</option></select>
    <?php endif; ?>
    <?php if (isset($errors['parent'])): ?>
      <span class="error"><?= $errors['parent'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $category["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/categories"><button type="button">Cancel</button></a>
  </form>
</div>
