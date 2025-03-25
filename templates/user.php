<div class="main">
  <form method="POST" action="/user/<?= $user['id'] > 0 ? $user['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= $user['username'] ?>"<?= isset($errors['username']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['username'])): ?>
      <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" value="<?= $user['first_name'] ?>"<?= isset($errors['first_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['first_name'])): ?>
      <span class="error"><?= $errors['first_name'] ?></span>
    <?php endif; ?>
    <label for="first_name">Last Name</label>
    <input type="text" name="last_name" value="<?= $user['last_name'] ?>"<?= isset($errors['last_name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['last_name'])): ?>
      <span class="error"><?= $errors['last_name'] ?></span>
    <?php endif; ?>
    <label for="email">Email</label>
    <input type="text" name="email" value="<?= $user['email'] ?>"<?= isset($errors['email']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['email'])): ?>
      <span class="error"><?= $errors['email'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $user["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/users"><button type="button">Cancel</button></a>
  </form>
</div>
