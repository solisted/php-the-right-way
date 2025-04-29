<div class="main">
  <form method="POST" action="/attribute/<?= $attribute['id'] > 0 ? $attribute['id'] : "add" ?>">
    <input type="hidden" name="id" value="<?= $attribute['id'] ?>"/>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $attribute['name'] ?>"<?= isset($errors['name']) ? ' class="error"' : "" ?>/>
    <?php if (isset($errors['name'])): ?>
      <span class="error"><?= $errors['name'] ?></span>
    <?php endif; ?>
    <button type="submit"><?= $attribute["id"] == 0 ? "Add" : "Update" ?></button>
    <a href="/attributes"><button type="button">Cancel</button></a>
  </form>
</div>
