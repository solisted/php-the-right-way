<?php
  $can_update = sl_auth_is_authorized("UpdateAction") && $action['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateAction") && $action['id'] === 0;
?>
<div class="main">
  <form method="POST" action="/action/<?= $action['id'] > 0 ? $action['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $action['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $action['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="description">Description</label>
    <input type="text" name="description" id="description" value="<?= $action['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['description'])): ?>
      <span class="error"><?= $errors['description'] ?></span>
    <?php endif; ?>
    <?php if ($can_update || $can_create): ?>
    <button type="submit"><?= $action["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/actions"><button type="button">Cancel</button></a>
    <?php endif; ?>
  </form>
</div>
