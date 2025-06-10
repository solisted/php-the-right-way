<div class="login">
  <form method="POST" action="/forgot-password">
    <?php if ($show_reset === false): ?>
    <h3>Forgot password</h3>
    <?php if (isset($reset_message)): ?>
    <span class="confirm"><?= $reset_message ?></span>
    <a class="middle" href="/login">Go back to login</a>
    <?php else: ?>
    <input type="hidden" name="action" value="request_link"/>
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= $reset['username'] ?>"<?= isset($errors['username']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors["username"])): ?>
    <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
    <button type="submit">Reset</button>
    <a href="/login">Log in</a>
    <?php endif; ?>
    <?php else: ?>
    <h3>Reset password</h3>
    <input type="hidden" name="action" value="reset_password"/>
    <label for="password">Password</label>
    <input type="text" name="password" id="password" value="<?= $reset['password'] ?>"<?= isset($errors['password']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors["password"])): ?>
    <span class="error"><?= $errors['password'] ?></span>
    <?php endif; ?>
    <label for="password1">Repeat password</label>
    <input type="text" name="password1" id="password1" value="<?= $reset['password1'] ?>"<?= isset($errors['password1']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors["password1"])): ?>
    <span class="error"><?= $errors['password1'] ?></span>
    <?php endif; ?>
    <button type="submit">Reset</button>
    <a href="/login">Go back to login</a>
    <?php endif; ?>
  </form>
</div>
