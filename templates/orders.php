<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="25%">Number</th>
        <th width="70%">Created</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($orders) > 0): ?>
      <?php foreach ($orders as $order): ?>
      <tr>
        <td align="right"><?= $order["id"] ?></td>
        <?php if (sl_auth_is_authorized_any(["ReadOrder", "UpdateOrder"])): ?>
        <td><a href="/order/<?= $order["id"] ?>"><?= $order["number"] ?></a></td>
        <?php else: ?>
        <td><?= $order["number"] ?></td>
        <?php endif; ?>
        <td><?= $order["created"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No orders found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
