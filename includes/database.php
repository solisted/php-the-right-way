<?php
declare(strict_types=1);

function sl_database_get_connection(): PDO
{
    return new PDO("mysql:dbname=ivan;host=localhost", "ivan", "ivan");
}

function sl_database_is_unique_username(PDO $connection, string $username, int $user_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id <> :id");
    $statement->bindValue(":username", $username, PDO::PARAM_STR);
    $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_name(PDO $connection, string $first_name, string $last_name, int $user_id): bool
{
    $statement = $connection->prepare(
        "SELECT COUNT(*) FROM users WHERE first_name = :first_name AND last_name = :last_name AND id <> :id"
    );
    $statement->bindValue(":first_name", $first_name, PDO::PARAM_STR);
    $statement->bindValue(":last_name", $last_name, PDO::PARAM_STR);
    $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_email(PDO $connection, string $email, int $user_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id");
    $statement->bindValue(":email", $email, PDO::PARAM_STR);
    $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_rolename(PDO $connection, string $name, int $role_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM roles WHERE name = :name AND id <> :id");
    $statement->bindValue(":name", $name, PDO::PARAM_STR);
    $statement->bindValue(":id", $role_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_actionname(PDO $connection, string $name, int $action_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM actions WHERE name = :name AND id <> :id");
    $statement->bindValue(":name", $name, PDO::PARAM_STR);
    $statement->bindValue(":id", $action_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_categoryname(PDO $connection, string $name, int $category_id): bool
{
    $statement = $connection->prepare("SELECT COUNT(*) FROM categories WHERE name = :name AND id <> :id");
    $statement->bindValue(":name", $name, PDO::PARAM_STR);
    $statement->bindValue(":id", $category_id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}
