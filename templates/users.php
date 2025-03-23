<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Users</title>
  <link rel="stylesheet" href="/default.css"/>
</head>
<body>
  <table>
    <thead>
      <tr>
        <th width="5%">#</th>
        <th width="20%">Username</th>
        <th width="25%">First Name</th>
        <th width="25%">Last Name</th>
        <th width="25%">Email</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
      <tr>
        <td align="right"><?= $user["id"] ?></td>
        <td><a href="/user/<?= $user["id"] ?>"><?= $user["username"] ?></a></td>
        <td><?= $user["first_name"] ?></td>
        <td><?= $user["last_name"] ?></td>
        <td><?= $user["email"] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
