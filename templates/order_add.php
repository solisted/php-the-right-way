<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/order/add">
    <input type="hidden" name="action" value="add_order"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <label for="search_term">Search Term / Customer</label>
      <input type="text" name="search_term" id="search_term" value="<?= $search_term ?>"<?= isset($errors['search_term']) ? ' class="error"' : "" ?>/>
      <?php if (isset($errors['search_term'])): ?>
        <span class="error"><?= $errors['search_term'] ?></span>
      <?php endif; ?>
    </div>
    <div class="row">
      <button type="submit" name="action" value="search_customer">Search</button>
      <a href="/orders"><button type="button" class="secondary">Cancel</button></a>
    </div>
  </form>
  <?php if (sl_request_post_string_equals("action", "search_customer")): ?>
  <table>
    <thead>
      <tr>
        <th width="70%">Name</th>
        <th width="25%">Email</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($found_customers) > 0): ?>
      <?php foreach ($found_customers as $customer): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadCustomer", "UpdateCustomer"])): ?>
        <td><a href="/customer/<?= $customer["id"] ?>"><?= $customer["first_name"] ?>&nbsp;<?= $customer["last_name"] ?></a></td>
        <?php else: ?>
        <td><?= $customer["first_name"] ?>&nbsp;<?= $customer["last_name"] ?></td>
        <?php endif; ?>
        <td><?= $customer["email"] ?></td>
        <td align="right">
          <?php if (sl_auth_is_authorized("UpdateOrder")): ?>
          <form class="hidden" method="POST" action="/order/add">
            <input type="hidden" name="action" value="add_order"/>
            <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <button type="submit"><img class="icon" src="/icons/plus.png"/></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr><td colspan="3" align="center">No customers match your criteria</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
