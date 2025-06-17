<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/product/<?= $product['id'] > 0 ? $product['id'] : "add" ?>?tab=<?= $tab_number ?>">
    <input type="hidden" name="action" value="add_update_product"/>
    <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
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
  <ul class="tabbar">
    <li class="<?= $tab_number === 0 ? "active" : "" ?>">
      <a href="/product/<?= $product['id'] ?>?tab=0">Attributes</a>
    </li>
    <li class="<?= $tab_number === 1 ? "active" : "" ?>">
      <a href="/product/<?= $product['id'] ?>?tab=1">Images</a>
    </li>
  </ul>
  <?php if ($tab_number === 0): ?>
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
          <form class="hidden" method="POST" action="/product/<?= $product['id'] ?>?tab=<?= $tab_number ?>">
            <input type="hidden" name="action" value="delete_attribute"/>
            <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
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
  <form method="POST" action="/product/<?= $product['id'] ?>?tab=<?= $tab_number ?>">
    <input type="hidden" name="action" value="add_attribute"/>
    <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
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
  <?php endif; ?>
  <?php if ($tab_number === 1): ?>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="70%">File Name</th>
        <th width="20%">Type</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($product_images) > 0): ?>
      <?php foreach ($product_images as $product_image): ?>
      <tr>
        <td align="right"><?= $product_image['id'] ?></td>
        <td>
          <a href="/image/<?= $product_image['id'] ?>"><?= basename($product_image['orig_filename']) ?></a>
        </td>
        <td><?= $product_image['mime_type'] ?></td>
        <td align="right">
          <form class="hidden" method="POST" action="/product/<?= $product['id'] ?>?tab=<?= $tab_number ?>">
            <input type="hidden" name="action" value="delete_image"/>
            <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="image_id" value="<?= $product_image['id'] ?>"/>
            <button type="submit">&#128473;</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No images found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  <form enctype="multipart/form-data" method="POST" action="/product/<?= $product['id'] ?>?tab=<?= $tab_number ?>">
    <input type="hidden" name="action" value="add_image"/>
    <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <label for="image">Image</label>
    <input type="file" id="image" name="image" accept="image/png, image/jpeg" <?= isset($errors['image']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['image'])): ?>
      <span class="error"><?= $errors['image'] ?></span>
    <?php endif; ?>
    <button type="submit">Upload</button>
  </form>
  <?php endif; ?>
  <?php endif ?>
</div>
