<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="15%">Username</th>
        <th width="22%">Name</th>
        <th width="20%">Email</th>
        <th width="15%">Status</th>
        <th width="20%">Updated</th>
        <th width="7%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($users) > 0): ?>
      <?php foreach ($users as $user): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadUser", "UpdateUser"])): ?>
        <td><a href="/user/<?= $user["id"] ?>"><?= $user["username"] ?></a></td>
        <?php else: ?>
        <td><?= $user["username"] ?></td>
        <?php endif; ?>
        <td><?= $user["first_name"] ?>&nbsp;<?= $user["last_name"] ?></td>
        <td><?= $user["email"] ?></td>
        <td><?= $user["status"] ?></td>
        <td><?= $user["updated"] ?></td>
        <td align="right">
          <?php if (sl_auth_is_authorized("UpdateUser")): ?>
          <form class="hidden" method="POST" action="/user/<?= $user['id'] ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="return" value="users"/>
            <?php if ($user["status_id"] == SL_USER_ACTIVE_STATUS_ID): ?>
            <button type="submit" name="action" value="lock_user"><img class="icon" src="/icons/lock.png"/></button>
            <button type="submit" name="action" value="delete_user"><img class="icon" src="/icons/delete.png"/></button>
            <?php elseif ($user["status_id"] == SL_USER_LOCKED_STATUS_ID): ?>
            <button type="submit" name="action" value="unlock_user"><img class="icon" src="/icons/unlock.png"/></button>
            <?php elseif ($user["status_id"] == SL_USER_DELETED_STATUS_ID): ?>
            <button type="submit" name="action" value="restore_user"><img class="icon" src="/icons/restore.png"/></button>
            <?php endif; ?>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="6" align="center">No users found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
