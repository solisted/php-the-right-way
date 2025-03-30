<div class="main">
  <form method="POST" action="/user/<?= $user['id'] > 0 ? $user['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= $user['username'] ?>"<?= isset($errors['username']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['username'])): ?>
      <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" id="first_name" value="<?= $user['first_name'] ?>"<?= isset($errors['first_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['first_name'])): ?>
      <span class="error"><?= $errors['first_name'] ?></span>
    <?php endif; ?>
    <label for="last_name">Last Name</label>
    <input type="text" name="last_name" id="last_name" value="<?= $user['last_name'] ?>"<?= isset($errors['last_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['last_name'])): ?>
      <span class="error"><?= $errors['last_name'] ?></span>
    <?php endif; ?>
    <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $user['email'] ?>"<?= isset($errors['email']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['email'])): ?>
      <span class="error"><?= $errors['email'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $user["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/users"><button type="button">Cancel</button></a>
  </form>
  <?php if ($user['id'] > 0): ?>
  <h3>Roles</h3>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="30%">Name</th>
        <th width="60%">Description</th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($user_roles) > 0): ?>
      <?php foreach ($user_roles as $role): ?>
      <tr>
        <td align="right"><?= $role["id"] ?></td>
        <td><?= $role["name"] ?></td>
        <td><?= $role["description"] ?></td>
        <td align="right">
          <form class="hidden" method="POST" action="/user/<?= $user['id'] ?>">
            <input type="hidden" name="action" value="delete_role"/>
            <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
            <input type="hidden" name="role_id" value="<?= $role['id'] ?>"/>
            <button type="submit">&#128473;</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="5" align="center">No roles found</td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  <form class="horizontal" method="POST" action="/user/<?= $user['id'] ?>">
    <input type="hidden" name="action" value="add_role"/>
    <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
    <label for="role">Role</label>
    <?php if (count($other_roles) > 0): ?>
    <select name="role_id" id="role">
      <?php foreach ($other_roles as $role): ?>
      <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <?php else: ?>
    <select name="role_id" disabled><option>No roles found</option></select>
    <?php endif ?>
    <button type="submit" <?= count($other_roles) === 0 ? "disabled" : "" ?>>Add</button>
  </form>
  <?php endif ?>
</div>
