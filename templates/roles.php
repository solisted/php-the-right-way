<div class="main">
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="30%">Name</th>
        <th width="65%">Description</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($roles) > 0): ?>
      <?php foreach ($roles as $role): ?>
      <tr>
        <td align="right"><?= $role["id"] ?></td>
        <?php if (sl_auth_is_authorized_any(["ReadRole", "UpdateRole"])): ?>
        <td><a href="/role/<?= $role["id"] ?>"><?= $role["name"] ?></a></td>
        <?php else: ?>
        <td><?= $role["name"] ?></td>
        <?php endif; ?>
        <td><?= $role["description"] ?></td>
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
