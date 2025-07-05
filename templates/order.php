<?php
  $can_update = sl_auth_is_authorized("UpdateOrder") && $order['id'] > 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/order/<?= $order['id'] . "?tab={$tab_number}" ?>">
    <input type="hidden" name="action" value="update_order"/>
    <input type="hidden" name="id" value="<?= $order['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>

    <label for="number">Number</label>
    <input type="text" name="number" id="number" value="<?= $order['number'] ?>" readonly/>
    <label for="updated">Updated</label>
    <input type="text" name="updated" id="updated" value="<?= $order['updated'] ?>" readonly/>
    <label for="status">Status</label>
    <?php if (count($statuses) > 0): ?>
    <select name="status_id" id="status"<?= isset($errors['status_id']) ? ' class="error"' : "" ?>>
      <option value="0">Select status</option>
      <?php foreach ($statuses as $status): ?>
      <option value="<?= $status['id'] ?>" <?= $status['name'] == $order['status'] ? "selected" : "" ?>>
        <?= $status['name'] ?>
      </option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['status_id'])): ?>
      <span class="error"><?= $errors['status_id'] ?></span>
    <?php endif; ?>
    <?php else: ?>
    <select name="status_id" disabled><option>No statuses found</option></select>
    <?php endif; ?>

    <?php if ($can_update): ?>
    <button type="submit">Update</button>
    <a href="/orders"><button type="button">Cancel</button></a>
    <?php endif; ?>
  </form>
</div>
