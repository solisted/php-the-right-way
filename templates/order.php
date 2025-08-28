<?php
  $can_update = sl_auth_is_authorized("UpdateOrder") && $order['id'] > 0;
?>
<div class="main">
  <?php sl_template_render_flash_message() ?>
  <form method="POST" action="/order/<?= $order['id'] . "?tab={$tab_number}" ?>">
    <input type="hidden" name="action" value="update_order"/>
    <input type="hidden" name="id" value="<?= $order['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <div class="left-column">
        <label for="number">Number</label>
        <input type="text" name="number" id="number" value="<?= $order['number'] ?>" readonly/>
        <label for="customer">Customer</label>
        <input type="text" name="customer" id="customer" value="<?= $order['first_name'] ?>&nbsp;<?= $order['last_name'] ?>" readonly/>
      </div>
      <div class="right-column">
        <label for="updated">Updated</label>
        <input type="text" name="updated" id="updated" value="<?= $order['updated'] ?>" readonly/>
        <label for="status">Status</label>
        <?php if (count($statuses) > 0): ?>
        <select name="status_id" id="status"<?= isset($errors['status_id']) ? ' class="error"' : "" ?>>
          <option value="0">Select status</option>
          <?php foreach ($statuses as $status): ?>
          <option value="<?= $status['id'] ?>" <?= $status['id'] == $order['status_id'] ? "selected" : "" ?>>
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
      </div>
    </div>
    <div class="row">
      <?php if ($can_update): ?>
      <button type="submit">Update</button>
      <a href="/orders"><button type="button">Cancel</button></a>
      <?php endif; ?>
    </div>
  </form>
  <?php if ($order['id'] > 0): ?>
  <ul class="tabbar">
    <li class="<?= $tab_number === 0 ? "active" : "" ?>">
      <a href="/order/<?= $order['id'] ?>?tab=0">Items</a>
    </li>
    <li class="<?= $tab_number === 1 ? "active" : "" ?>">
      <a href="/order/<?= $order['id'] ?>?tab=1">History</a>
    </li>
  </ul>
  <?php if ($tab_number === 0): ?>
  <table>
    <thead>
      <tr>
        <th width="55%">Product</th>
        <th width="15%">SKU</th>
        <th width="10%">Price</th>
        <th width="10%">Quantity</th>
        <th width="10%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($order_items) > 0): ?>
      <?php foreach ($order_items as $item): ?>
      <tr>
        <?php if (sl_auth_is_authorized("ReadProduct")): ?>
        <td><a href="/product/<?= $item['product_id']?>"><?= $item['name'] ?></a></td>
        <?php else: ?>
        <td><?= $item['name'] ?></td>
        <?php endif; ?>
        <td><?= $item['sku'] ?></td>
        <td align="right"><?= number_format($item["price"] / 100, 2) ?></td>
        <td align="right"><?= $item['quantity'] ?></td>
        <td align="right">
          <?php if ($can_update): ?>
          <form class="hidden" method="POST" action="/order/<?= $order['id'] ?>?tab=<?= $tab_number ?>">
            <input type="hidden" name="id" value="<?= $order['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>"/>
            <button type="submit" name="action" value="decrease_item"<?= intval($item['quantity']) < 2 ? "disabled" : ""?>><img class="icon" src="/icons/minus.png"/></button>
            <button type="submit" name="action" value="increase_item"><img class="icon" src="/icons/plus.png"/></button>
            <button type="submit" name="action" value="delete_item"><img class="icon" src="/icons/delete.png"/></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No items found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if ($can_update): ?>
  <form method="POST" action="/order/<?= $order['id'] ?>?tab=<?= $tab_number ?>">
    <input type="hidden" name="id" value="<?= $order['id'] ?>"/>
    <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
    <div class="row">
      <div class="left-column">
        <label for="sku">Search term / SKU</label>
        <input type="text" name="sku" id="sku" value="<?= $order_item['sku'] ?>"<?= isset($errors['sku']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['sku'])): ?>
          <span class="error"><?= $errors['sku'] ?></span>
        <?php endif; ?>
      </div>
      <div class="right-column">
        <label for="quantity">Quantity</label>
        <input type="text" name="quantity" id="quantity" value="<?= $order_item['quantity'] ?>"<?= isset($errors['quantity']) ? ' class="error"' : "" ?>/>
        <?php if (isset($errors['quantity'])): ?>
          <span class="error"><?= $errors['quantity'] ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
      <button type="submit" name="action" value="search_item">Search</button>
      <button type="submit" class="secondary" name="action" value="add_item">Add</button>
    </div>
  </form>
  <?php endif; ?>
  <?php if (sl_request_post_string_equals("action", "search_item")): ?>
  <table>
    <thead>
      <tr>
        <th width="45%">Name</th>
        <th width="15%">SKU</th>
        <th width="15%">Category</th>
        <th width="10%">Price</th>
        <th width="15%">Quantity</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($found_products) > 0): ?>
      <?php $product_price_ids = array_column($order_items, "product_price_id", null); ?>
      <?php foreach ($found_products as $product): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadProduct", "UpdateProduct"])): ?>
        <td><a href="/product/<?= $product["id"] ?>"><?= $product["name"] ?></a></td>
        <?php else: ?>
        <td><?= $product["name"] ?></td>
        <?php endif; ?>
        <td><?= $product["sku"] ?></td>
        <td><?= $product["category"] ?></td>
        <td align="right"><?= number_format($product["price"] / 100, 2) ?></td>
        <td align="right">
          <?php if (sl_auth_is_authorized("UpdateOrder")): ?>
          <form class="hidden" method="POST" action="/order/<?= $order['id'] ?>?tab=<?= $tab_number ?>">
            <input type="hidden" name="action" value="add_product"/>
            <input type="hidden" name="id" value="<?= $order['id'] ?>"/>
            <input type="hidden" name="price_id" value="<?= $product['product_price_id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <input type="number" name="quantity" min="1" max="10" value="1"/>
            <button type="submit" <?= in_array($product['product_price_id'], $product_price_ids) ? "disabled" : "" ?>>&#10010;</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr><td colspan="5" align="center">No products match your criteria</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
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
      <?php if (count($order_history) > 0): ?>
      <?php foreach ($order_history as $status_item): ?>
      <tr>
        <td align="right"><?= ($status_item['id'] == $order['status_history_id']) ? "<img class=\"icon\" src=\"/icons/check.png\"/>" : ""?></td>
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
