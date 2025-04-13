<div class="sidebar">
  <ul class="sidebar">
    <li class="header">Catalog</li>
    <li <?= sl_request_is_uri('/categor') ? 'class="active"' : "" ?>>
      <a href="/categories">Categories</a>
      <a href="/category/add">+</a>
    </li>
    <li class="header">Access control</li>
    <?php if (sl_auth_is_authorized("ListUsers")): ?>
    <li <?= sl_request_is_uri('/user') ? 'class="active"' : "" ?>><a href="/users">Users</a>
    <?php if (sl_auth_is_authorized("CreateUser")): ?>
      <a href="/user/add">+</a>
    <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListRoles")): ?>
    <li <?= sl_request_is_uri('/role') ? 'class="active"' : "" ?>><a href="/roles">Roles</a>
    <?php if (sl_auth_is_authorized("CreateRole")): ?>
      <a href="/role/add">+</a>
    <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListActions")): ?>
    <li <?= sl_request_is_uri('/action') ? 'class="active"' : "" ?>><a href="/actions">Actions</a>
    <?php if (sl_auth_is_authorized("CreateAction")): ?>
      <a href="/action/add">+</a>
    <?php endif; ?>
    </li>
    <?php endif; ?>
    <li class="header">Session</li>
    <li><a href="/logout">Logout</a></li>
  </ul>
</div>
