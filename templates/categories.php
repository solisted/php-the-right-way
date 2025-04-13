<div class="main">
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="85%">Name</th>
        <th width="10%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $category): ?>
      <tr>
        <td align="right"><?= $category['id'] ?></td>
        <td>
          <?= $category['depth'] > 0 ? str_repeat("&emsp;", $category['depth'] - 1) . "\u{2514}" : "" ?>
          <a href="/category/<?= $category['id'] ?>"><?= $category['name'] ?></a>
        </td>
        <td align="right">
          <form class="hidden" method="POST" action="/category/<?= $category['id'] ?>">
            <input type="hidden" name="id" value="<?= $category['id'] ?>"/>
            <button type="submit" <?= $category['depth'] == 0 ? "disabled" : "" ?>>&#128473;</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</ul>
</div>
