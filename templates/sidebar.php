<div class="sidebar">
  <ul class="sidebar">
    <li class="header">Access control</li>
    <li <?= sl_request_is_uri('/user') ? 'class="active"' : "" ?>><a href="/users">Users</a><a href="/user/add">+</a></li>
    <li <?= sl_request_is_uri('/role') ? 'class="active"' : "" ?>><a href="/roles">Roles</a><a href="/role/add">+</a></li>
    <li <?= sl_request_is_uri('/action') ? 'class="active"' : "" ?>><a href="/actions">Actions</a><a href="/action/add">+</a></li>
  </ul>
</div>
