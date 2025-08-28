<?php
  $can_update = sl_auth_is_authorized("UpdateUser") && $user['id'] > 0;
  $can_create = sl_auth_is_authorized("CreateUser") && $user['id'] === 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/user/<?= $user['id'] > 0 ? $user['id'] . "?tab={$tab_number}" : "add" ?>">
    <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <div class="left-column">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $user['username'] ?>"<?= isset($errors['username']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['username'])): ?>
          <span class="error"><?= $errors['username'] ?></span>
        <?php endif; ?>
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" value="<?= $user['first_name'] ?>"<?= isset($errors['first_name']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['first_name'])): ?>
          <span class="error"><?= $errors['first_name'] ?></span>
        <?php endif; ?>
        <?php if ($can_update || $can_create): ?>
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" value="<?= $user['last_name'] ?>"<?= isset($errors['last_name']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['last_name'])): ?>
          <span class="error"><?= $errors['last_name'] ?></span>
        <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="right-column">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?= $user['email'] ?>"<?= isset($errors['email']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['email'])): ?>
          <span class="error"><?= $errors['email'] ?></span>
        <?php endif; ?>
        <?php if ($can_update || $can_create): ?>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" value="<?= $user['password'] ?>" <?= isset($errors['password']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['password'])): ?>
          <span class="error"><?= $errors['password'] ?></span>
        <?php endif; ?>
        <label for="password1">Repeat Password</label>
        <input type="password" name="password1" id="password1" value="<?= $user['password1'] ?>" <?= isset($errors['password1']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['password1'])): ?>
          <span class="error"><?= $errors['password1'] ?></span>
        <?php endif; ?>
        <?php else: ?>
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" value="<?= $user['last_name'] ?>"<?= isset($errors['last_name']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['last_name'])): ?>
          <span class="error"><?= $errors['last_name'] ?></span>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
    <?php if ($can_create || $can_update): ?>
      <button type="submit" name="action" value="add_update_user"><?= $user["id"] == 0 ? "Add" : "Update" ?></button>
      <?php if ($user["status_id"] == SL_USER_ACTIVE_STATUS_ID): ?>
      <button type="submit" class="secondary" name="action" value="lock_user">Lock</button>
      <button type="submit" class="secondary" name="action" value="delete_user">Delete</button>
      <?php elseif ($user["status_id"] == SL_USER_LOCKED_STATUS_ID): ?>
      <button type="submit" class="secondary" name="action" value="unlock_user">Unlock</button>
      <?php elseif ($user["status_id"] == SL_USER_DELETED_STATUS_ID): ?>
      <button type="submit" class="secondary" name="action" value="restore_user">Restore</button>
      <?php endif; ?>
      <a href="/users"><button type="button">Cancel</button></a>
    <?php endif; ?>
    </div>
  </form>
  <?php if ($user['id'] > 0): ?>
  <ul class="tabbar">
    <li class="<?= $tab_number === 0 ? "active" : "" ?>">
      <a href="/user/<?= $user['id'] ?>?tab=0">Roles</a>
    </li>
    <li class="<?= $tab_number === 1 ? "active" : "" ?>">
      <a href="/user/<?= $user['id'] ?>?tab=1">History</a>
    </li>
  </ul>
  <?php if ($tab_number === 0): ?>
  <table>
    <thead>
      <tr>
        <th width="35%">Name</th>
        <th width="60%">Description</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($user_roles) > 0): ?>
      <?php foreach ($user_roles as $role): ?>
      <tr>
        <td><?= $role["name"] ?></td>
        <td><?= $role["description"] ?></td>
        <td align="right">
          <?php if ($can_update): ?>
          <form class="hidden" method="POST" action="/user/<?= $user['id'] . "?tab={$tab_number}"?>">
            <input type="hidden" name="action" value="delete_role"/>
            <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="role_id" value="<?= $role['id'] ?>"/>
            <button type="submit"><img class="icon" src="/icons/delete.png"/></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="3" align="center">No roles found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if ($can_update): ?>
  <form class="horizontal" method="POST" action="/user/<?= $user['id'] . "?tab={$tab_number}"?>">
    <input type="hidden" name="action" value="add_role"/>
    <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <label for="role">Role</label>
    <?php if (count($other_roles) > 0): ?>
    <select name="role_id" id="role">
      <?php foreach ($other_roles as $role): ?>
      <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="role_id" disabled><option>No roles found</option></select>
    <?php endif; ?>
    <button type="submit" <?= count($other_roles) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif; ?>
  <?php elseif ($tab_number === 1): ?>
  <table>
    <thead>
      <tr>
        <th width="1em"></th>
        <th width="20%">Status</th>
        <th width="80%">Created</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($user_history) > 0): ?>
      <?php foreach ($user_history as $status_item): ?>
      <tr>
        <td align="right"><?= ($status_item['id'] == $user['status_history_id']) ? "<img class=\"icon\" src=\"/icons/check.png\"/>" : ""?></td>
        <td><?= $status_item['name'] ?></td>
        <td><?= $status_item['created'] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="3" align="center">No history items found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php endif; ?>
  <?php endif; ?>
</div>
