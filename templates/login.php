<div class="login">
  <form method="POST" action="/login">
    <h3>Please log in</h3>
    <?php if (isset($auth_error)): ?>
    <span class="alert"><?= $auth_error ?></span>
    <?php endif; ?>
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= $username ?>"<?= isset($errors['username']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors["username"])): ?>
    <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" value="<?= $password ?>"<?= isset($errors['password']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors["password"])): ?>
    <span class="error"><?= $errors['password'] ?></span>
    <?php endif; ?>
    <button type="submit">Login</button>
  </form>
</div>
