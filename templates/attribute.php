<?php
  $can_update = sl_auth_is_authorized("UpdateAttribute") && $attribute['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateAttribute") && $attribute['id'] === 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/attribute/<?= $attribute['id'] > 0 ? $attribute['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $attribute['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $attribute['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <?php if ($can_update || $can_create): ?>
    <button type="submit"><?= $attribute["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/attributes"><button type="button">Cancel</button></a>
    <?php endif; ?>
  </form>
</div>
