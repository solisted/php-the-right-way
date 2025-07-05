<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="60%">Name</th>
        <th width="35%">Email</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($customers) > 0): ?>
      <?php foreach ($customers as $customer): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadCustomer", "UpdateCustomer"])): ?>
        <td><a href="/customer/<?= $customer["id"] ?>"><?= $customer["first_name"] . "&nbsp;" . $customer["last_name"] ?></a></td>
        <?php else: ?>
        <td><?= $customer["first_name"] . "&nbsp;" . $customer["last_name"] ?></td>
        <?php endif; ?>
        <td><?= $customer["email"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="2" align="center">No customers found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
