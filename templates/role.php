<div class="main">
  <form method="POST" action="/role/<?= $role['id'] > 0 ? $role['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $role['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="description">Description</label>
    <input type="text" name="description" id="description" value="<?= $role['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['description'])): ?>
      <span class="error"><?= $errors['description'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $role["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/roles"><button type="button">Cancel</button></a>
  </form>
  <?php if ($role['id'] > 0): ?>
  <h3>Actions</h3>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="30%">Name</th>
        <th width="65%">Description</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($role_actions) > 0): ?>
      <?php foreach ($role_actions as $action): ?>
      <tr>
        <td align="right"><?= $action["id"] ?></td>
        <td><?= $action["name"] ?></td>
        <td><?= $action["description"] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No roles found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  <form class="horizontal" method="POST" action="/role/<?= $role['id'] ?>">
    <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
    <label for="action">Action</label>
    <?php if (count($other_actions) > 0): ?>
    <select name="action_id" id="action">
      <?php foreach ($other_actions as $action): ?>
      <option value="<?= $action['id'] ?>"><?= $action['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="action_id" disabled><option>No actions found</option></select>
    <?php endif ?>
    <button type="submit" <?= count($other_actions) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif ?>
</div>
