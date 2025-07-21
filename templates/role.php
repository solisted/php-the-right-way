<?php
  $can_update = sl_auth_is_authorized("UpdateRole") && $role['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateRole") && $role['id'] === 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/role/<?= $role['id'] > 0 ? $role['id'] : "add" ?><?= $role['id'] > 0 && $page > 1 ? "?page={$page}&size={$size}" : ""?>">
    <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <div class="left-column">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= $role['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['name'])): ?>
          <span class="error"><?= $errors['name'] ?></span>
        <?php endif; ?>
      </div>
      <div class="right-column">
        <label for="description">Description</label>
        <input type="text" name="description" id="description" value="<?= $role['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['description'])): ?>
          <span class="error"><?= $errors['description'] ?></span>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($can_update || $can_create): ?>
    <div class="row">
      <button type="submit"><?= $role["id"] == 0 ? "Add" : "Update" ?></button>
      <a href="/roles"><button type="button">Cancel</button></a>
    </div>
    <?php endif; ?>
  </form>
  <?php if ($role['id'] > 0): ?>
  <h3>Actions</h3>
  <table>
    <thead>
      <tr>
        <th width="35%">Name</th>
        <th width="60%">Description</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($role_actions) > 0): ?>
      <?php foreach ($role_actions as $action): ?>
      <tr>
        <td><?= $action["name"] ?></td>
        <td><?= $action["description"] ?></td>
        <td align="right">
          <?php if ($can_update): ?>
          <form class="hidden" method="POST" action="/role/<?= $role['id'] ?>">
            <input type="hidden" name="action" value="delete_action"/>
            <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="action_id" value="<?= $action['id'] ?>"/>
            <button type="submit">&#128473;</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="3" align="center">No roles found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
  <?php if ($can_update): ?>
  <form class="horizontal" method="POST" action="/role/<?= $role['id'] ?>">
    <input type="hidden" name="action" value="add_action"/>
    <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <label for="action">Action</label>
    <?php if (count($other_actions) > 0): ?>
    <select name="action_id" id="action">
      <?php foreach ($other_actions as $action): ?>
      <option value="<?= $action['id'] ?>"><?= $action['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="action_id" disabled><option>No actions found</option></select>
    <?php endif ?>
    <button type="submit" <?= count($other_actions) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif ?>
  <?php endif ?>
</div>
