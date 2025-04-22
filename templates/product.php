<div class="main">
  <form method="POST" action="/product/<?= $product['id'] > 0 ? $product['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $product['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="category">Category</label>
    <?php if (count($categories) > 0): ?>
    <select name="category_id" id="category"<?= isset($errors['category_id']) ? ' class="error"' : "" ?>>
      <option value="0">Select category</option>
      <?php foreach ($categories as $category): ?>
      <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? "selected" : "" ?> <?= $category["rgt"] - $category["lft"] > 1 ? "disabled" : "" ?>>
        <?= $category['depth'] > 0 ? str_repeat("&emsp;", $category['depth']) : "" ?>
        <?= $category['name'] ?>
      </option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['category_id'])): ?>
      <span class="error"><?= $errors['category_id'] ?></span>
    <?php endif; ?>
    <?php else: ?>
    <select name="category_id" disabled><option>No categories found</option></select>
    <?php endif; ?>
    <button type="submit"><?= $product["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/products"><button type="button">Cancel</button></a>
  </form>
</div>
