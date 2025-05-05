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
      <?php $can_have_children = $parent_category["products"] == 0 ?>
      <option value="<?= $parent_category['id'] ?>" <?= $parent_category['id'] == $parent_id ? "selected" : "" ?> <?= $can_have_children ? "" : "disabled"?>>
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
  <?php if ($category['id'] > 0 && $category['rgt'] == $category['lft'] + 1): ?>
  <h3>Attributes</h3>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="90%">Name</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($category_attributes) > 0): ?>
      <?php foreach ($category_attributes as $attribute): ?>
      <tr>
        <td align="right"><?= $attribute["id"] ?></td>
        <td><?= $attribute["name"] ?></td>
        <td align="right">
          <form class="hidden" method="POST" action="/category/<?= $category['id'] ?>">
            <input type="hidden" name="action" value="delete_attribute"/>
            <input type="hidden" name="id" value="<?= $category['id'] ?>"/>
            <input type="hidden" name="attribute_id" value="<?= $attribute['id'] ?>"/>
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
  <form class="horizontal" method="POST" action="/category/<?= $category['id'] ?>">
    <input type="hidden" name="action" value="add_attribute"/>
    <input type="hidden" name="id" value="<?= $category['id'] ?>"/>
    <label for="attribute">Attribute</label>
    <?php if (count($other_attributes) > 0): ?>
    <select name="attribute_id" id="attribute">
      <?php foreach ($other_attributes as $attribute): ?>
      <option value="<?= $attribute['id'] ?>"><?= $attribute['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="attribute_id" disabled><option>No attributes found</option></select>
    <?php endif ?>
    <button type="submit" <?= count($other_attributes) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif ?>
</div>
