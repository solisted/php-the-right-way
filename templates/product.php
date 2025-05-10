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
    <label for="description">Description</label>
    <textarea type="text" name="description" id="description" rows="8" <?= isset($errors['description']) ? ' class="error"' : "" ?>><?= $product['description'] ?></textarea>
    <?php if (isset($errors['description'])): ?>
      <span class="error"><?= $errors['description'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $product["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/products"><button type="button">Cancel</button></a>
  </form>
  <?php if ($product['id'] > 0): ?>
  <h3>Attributes</h3>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="30%">Name</th>
        <th width="60%">Value</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($product_attributes) > 0): ?>
      <?php foreach ($product_attributes as $product_attribute): ?>
      <tr>
        <td align="right"><?= $product_attribute['id'] ?></td>
        <td><?= $product_attribute['name'] ?></td>
        <td><?= $product_attribute['value'] ?></td>
        <td align="right">
          <form class="hidden" method="POST" action="/product/<?= $product['id'] ?>">
            <input type="hidden" name="action" value="delete_attribute"/>
            <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
            <input type="hidden" name="attribute_id" value="<?= $product_attribute['id'] ?>"/>
            <button type="submit">&#128473;</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No attributes found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  <form method="POST" action="/product/<?= $product['id'] ?>">
    <input type="hidden" name="action" value="add_attribute"/>
    <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
    <label for="action">Attribute</label>
    <?php if (count($other_attributes) > 0): ?>
    <select name="attribute_id" id="action">
      <?php foreach ($other_attributes as $product_attribute): ?>
      <option value="<?= $product_attribute['id'] ?>" <?= $product_attribute['id'] == $attribute['id'] ? "selected" : "" ?>><?= $product_attribute['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <label for="value">Value</label>
    <input type="text" name="value" id="value" value="<?= $attribute['value'] ?>"<?= isset($errors['value']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['value'])): ?>
      <span class="error"><?= $errors['value'] ?></span>
    <?php endif; ?>
    <?php else: ?>
    <select name="action_id" disabled><option>No attributes found</option></select>
    <label for="value">Value</label>
    <input type="text" name="value" id="value" disabled />
    <?php endif ?>
    <button type="submit" <?= count($other_attributes) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif ?>
</div>
