<div class="main">
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="20%">Username</th>
        <th width="25%">First Name</th>
        <th width="25%">Last Name</th>
        <th width="25%">Email</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($users) > 0): ?>
      <?php foreach ($users as $user): ?>
      <tr>
        <td align="right"><?= $user["id"] ?></td>
        <td><a href="/user/<?= $user["id"] ?>"><?= $user["username"] ?></a></td>
        <td><?= $user["first_name"] ?></td>
        <td><?= $user["last_name"] ?></td>
        <td><?= $user["email"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No users found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
<?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
