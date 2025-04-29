<div class="main">
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
        <td><a href="/attribute/<?= $attribute["id"] ?>"><?= $attribute["name"] ?></a></td>
        <td align="right">
          <form class="hidden" method="POST" action="/attribute/<?= $attribute['id'] ?>">
            <input type="hidden" name="action" value="delete"/>
            <input type="hidden" name="id" value="<?= $attribute['id'] ?>"/>
            <button type="submit">&#128473;</button>
          </form>
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
