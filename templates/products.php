<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="70%">Name</th>
        <?php if ($category_id === 0): ?>
        <th width="25%">Category</th>
        <?php endif; ?>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($products) > 0): ?>
      <?php foreach ($products as $product): ?>
      <tr>
        <td align="right"><?= $product["id"] ?></td>
        <td><a href="/product/<?= $product["id"] ?>"><?= $product["name"] ?></a></td>
        <?php if ($category_id === 0): ?>
        <td><?= $product["category"] ?></td>
        <?php endif; ?>
        <td align="right">
          <form class="hidden" method="POST" action="/product/<?= $product['id'] ?>?category=<?= $category_id ?>">
            <input type="hidden" name="action" value="delete_product"/>
            <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <button type="submit">&#128473;</button>
          </form>
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
