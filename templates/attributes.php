<div class="main">
  <?php sl_template_render_flash_message() ?>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="90%">Name</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($attributes) > 0): ?>
      <?php foreach ($attributes as $attribute): ?>
      <tr>
        <td align="right"><?= $attribute["id"] ?></td>
        <?php if (sl_auth_is_authorized_any(["ReadAttribute", "UpdateAttribute"])): ?>
        <td><a href="/attribute/<?= $attribute["id"] ?>"><?= $attribute["name"] ?></a></td>
        <?php else: ?>
        <td><?= $attribute["name"] ?></td>
        <?php endif; ?>
        <td align="right">
          <?php if (sl_auth_is_authorized("DeleteAttribute")): ?>
          <form class="hidden" method="POST" action="/attribute/<?= $attribute['id'] ?>">
            <input type="hidden" name="action" value="delete"/>
            <input type="hidden" name="id" value="<?= $attribute['id'] ?>"/>
            <input type="hidden" name="csrf" value="<?= sl_auth_get_current_csrf() ?>"/>
            <button type="submit">&#128473;</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="2" align="center">No attributes found</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
