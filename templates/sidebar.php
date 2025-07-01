<div class="sidebar">
  <ul class="sidebar">
    <?php if (sl_auth_is_authorized_any(["ListOrders", "ListCustomers"])): ?>
    <li class="header">Store</li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListOrders")): ?>
    <li <?= sl_request_is_uri('/orders') ? 'class="active"' : "" ?>><a href="/orders">Orders</a>
      <?php if (sl_auth_is_authorized("CreateOrder")): ?>
      <a href="/order/add">+</a>
      <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListCustomers")): ?>
    <li <?= sl_request_is_uri('/customer') ? 'class="active"' : "" ?>><a href="/customers">Customers</a>
      <?php if (sl_auth_is_authorized("CreateCustomer")): ?>
      <a href="/customer/add">+</a>
      <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized_any(["ListCategories", "ListProducts", "ListAttributes"])): ?>
    <li class="header">Catalog</li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListCategories")): ?>
    <li <?= sl_request_is_uri('/categor') ? 'class="active"' : "" ?>><a href="/categories">Categories</a>
      <?php if (sl_auth_is_authorized("CreateCategory")): ?>
      <a href="/category/add">+</a>
      <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListProducts")): ?>
    <li <?= sl_request_is_uri('/product') ? 'class="active"' : "" ?>><a href="/products">Products</a>
      <?php if (sl_auth_is_authorized("CreateProduct")): ?>
      <a href="/product/add">+</a>
      <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized("ListAttributes")): ?>
    <li <?= sl_request_is_uri('/attribute') ? 'class="active"' : "" ?>><a href="/attributes">Attributes</a>
      <?php if (sl_auth_is_authorized("CreateAttribute")): ?>
      <a href="/attribute/add">+</a>
      <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if (sl_auth_is_authorized_any(["ListUsers", "ListRoles", "ListActions"])): ?>
    <li class="header">Access control</li>
    <?php endif; ?>
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
