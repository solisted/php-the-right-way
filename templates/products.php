<div class="main">
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="70%">Name</th>
        <th width="25%">Category</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($products) > 0): ?>
      <?php foreach ($products as $product): ?>
      <tr>
        <td align="right"><?= $product["id"] ?></td>
        <td><a href="/product/<?= $product["id"] ?>"><?= $product["name"] ?></a></td>
        <td><?= $product["category"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No products found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
