<div class="main">
  <form method="POST" action="/action/<?= $action['id'] > 0 ? $action['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $action['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $action['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <label for="description">Description</label>
    <input type="text" name="description" value="<?= $action['description'] ?>"<?= isset($errors['description']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['description'])): ?>
      <span class="error"><?= $errors['description'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $action["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/actions"><button type="button">Cancel</button></a>
  </form>
</div>
