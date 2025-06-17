<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="65%">Name</th>
        <th class="number" width="20%">Products</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $category): ?>
      <tr>
        <td align="right"><?= $category['id'] ?></td>
        <?php if (sl_auth_is_authorized_any(["ReadCategory", "UpdateCategory"])): ?>
        <td>
          <?= $category['depth'] > 0 ? str_repeat("&emsp;", $category['depth'] - 1) . "\u{2514}" : "" ?>
          <a href="/category/<?= $category['id'] ?>"><?= $category['name'] ?></a>
        </td>
        <?php else: ?>
        <td><?= $category["name"] ?></td>
        <?php endif; ?>
        <td align="right">
          <?php if (sl_auth_is_authorized("ReadProduct")): ?>
          <?php if ($category['products'] > 0): ?>
          <a href="/products?category=<?= $category['id'] ?>"><?= $category['products'] ?></a>
          <?php else: ?>
          0
          <?php endif; ?>
          <?php else: ?>
          <?= $category['products'] ?>
          <?php endif; ?>
        </td>
        <td align="right">
          <?php if (sl_auth_is_authorized("DeleteCategory")): ?>
          <form class="hidden" method="POST" action="/category/<?= $category['id'] ?>">
            <input type="hidden" name="action" value="delete"/>
            <input type="hidden" name="id" value="<?= $category['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <?php $can_delete = $category['depth'] > 0 && $category['rgt'] == $category['lft'] + 1 && $category['products'] == 0; ?>
            <button type="submit" <?= $can_delete ? "" : "disabled" ?>>&#128473;</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</ul>
</div>
