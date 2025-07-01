<?php
  $can_update = sl_auth_is_authorized("UpdateCustomer") && $customer['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateCustomer") && $customer['id'] === 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/customer/<?= $customer['id'] > 0 ? $customer['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $customer['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" id="first_name" value="<?= $customer['first_name'] ?>"<?= isset($errors['first_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['first_name'])): ?>
      <span class="error"><?= $errors['first_name'] ?></span>
    <?php endif; ?>
    <label for="last_name">Last Name</label>
    <input type="text" name="last_name" id="last_name" value="<?= $customer['last_name'] ?>"<?= isset($errors['last_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['last_name'])): ?>
      <span class="error"><?= $errors['last_name'] ?></span>
    <?php endif; ?>
    <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $customer['email'] ?>"<?= isset($errors['email']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['email'])): ?>
      <span class="error"><?= $errors['email'] ?></span>
    <?php endif; ?>
    <?php if ($can_update || $can_create): ?>
    <button type="submit"><?= $customer["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/customers"><button type="button">Cancel</button></a>
    <?php endif; ?>
  </form>
</div>
