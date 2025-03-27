<div class="main">
  <form method="POST" action="/role/<?= $role['id'] > 0 ? $role['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $role['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $role['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="description">Description</label>
    <input type="text" name="description" value="<?= $role['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['description'])): ?>
      <span class="error"><?= $errors['description'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $role["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/roles"><button type="button">Cancel</button></a>
  </form>
</div>
