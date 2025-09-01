<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="25%">Name</th>
        <th width="33%">Description</th>
        <th width="15%">Status</th>
        <th width="20%">Updated</th>
        <th width="7%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($roles) > 0): ?>
      <?php foreach ($roles as $role): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadRole", "UpdateRole"])): ?>
        <td><a href="/role/<?= $role["id"] ?>"><?= $role["name"] ?></a></td>
        <?php else: ?>
        <td><?= $role["name"] ?></td>
        <?php endif; ?>
        <td><?= $role["description"] ?></td>
        <td><?= $role["status"] ?></td>
        <td><?= $role["updated"] ?></td>
        <td align="right">
          <?php if (sl_auth_is_authorized("UpdateRole")): ?>
          <form class="hidden" method="POST" action="/role/<?= $role['id'] ?>">
            <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="return" value="roles"/>
            <?php if ($role["status_id"] == SL_ROLE_ACTIVE_STATUS_ID): ?>
            <button type="submit" name="action" value="delete_role"><img class="icon" src="/icons/delete.png"/></button>
            <?php elseif ($role["status_id"] == SL_ROLE_DELETED_STATUS_ID): ?>
            <button type="submit" name="action" value="restore_role"><img class="icon" src="/icons/restore.png"/></button>
            <?php endif; ?>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No roles found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
<?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
