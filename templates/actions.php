<div class="main">
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="30%">Name</th>
        <th width="65%">Description</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($actions) > 0): ?>
      <?php foreach ($actions as $action): ?>
      <tr>
        <td align="right"><?= $action["id"] ?></td>
        <td><a href="/action/<?= $action["id"] ?>"><?= $action["name"] ?></a></td>
        <td><?= $action["description"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No actions found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
<?php sl_template_render_pager($url, $page, $size, $total_pages); ?>
</div>
