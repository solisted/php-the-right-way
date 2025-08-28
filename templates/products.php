<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="55%">Name</th>
        <th width="15%">SKU</th>
        <?php if ($category_id === 0): ?>
        <th width="15%">Category</th>
        <?php endif; ?>
        <th width="10%">Price</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($products) > 0): ?>
      <?php foreach ($products as $product): ?>
      <tr>
        <?php if (sl_auth_is_authorized_any(["ReadProduct", "UpdateProduct"])): ?>
        <td><a href="/product/<?= $product["id"] ?>"><?= $product["name"] ?></a></td>
        <?php else: ?>
        <td><?= $product["name"] ?></td>
        <?php endif; ?>
        <td><?= $product["sku"] ?></td>
        <?php if ($category_id === 0): ?>
        <td><?= $product["category"] ?></td>
        <?php endif; ?>
        <td align="right"><?= number_format($product["price"] / 100, 2) ?></td>
        <td align="right">
          <?php if (sl_auth_is_authorized("DeleteProduct")): ?>
          <form class="hidden" method="POST" action="/product/<?= $product['id'] ?>?category=<?= $category_id ?>">
            <input type="hidden" name="action" value="delete_product"/>
            <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <button type="submit"><img class="icon" src="/icons/delete.png"/></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No products found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages, ($category_id > 0) ? ["category" => $category_id] : null); ?>
</div>
