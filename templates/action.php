<?php
  $can_update = sl_auth_is_authorized("UpdateAction") && $action['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateAction") && $action['id'] === 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/action/<?= $action['id'] > 0 ? $action['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $action['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <div class="left-column">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= $action['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['name'])): ?>
          <span class="error"><?= $errors['name'] ?></span>
        <?php endif; ?>
      </div>
      <div class="right-column">
        <label for="description">Description</label>
        <input type="text" name="description" id="description" value="<?= $action['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['description'])): ?>
          <span class="error"><?= $errors['description'] ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
    <?php if ($can_update || $can_create): ?>
      <button type="submit"><?= $action["id"] == 0 ? "Add" : "Update" ?></button>
      <a href="/actions"><button type="button">Cancel</button></a>
    <?php endif; ?>
    </div>
  </form>
</div>
