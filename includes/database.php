<?php
declare(strict_types=1);

function sl_database_get_connection(): PDO
{
    return new PDO(SL_DATABASE_DSN, SL_DATABASE_USER, SL_DATABASE_PASSWORD);
}

function sl_database_is_unique_column(PDO $connection, string $table, string $column, string $value, int $id)
{
    if (preg_match("/^[a-z0-9_]+$/", $table) !== 1 ||
        preg_match("/^[a-z0-9_]+$/", $column) !== 1
    ) {
        trigger_error("Table and column names should have only alphanumeric characters and underscore", E_USER_ERROR);
    }

    $statement = $connection->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :value AND id <> :id");
    $statement->bindValue(":value", $value, PDO::PARAM_STR);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn(0) === 0;
}

function sl_database_is_unique_user_name(PDO $connection, string $first_name, string $last_name, int $user_id): bool
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

function sl_database_get_categories(PDO $connection): array
{
    $statement = $connection->query("SELECT n.id, n.name, n.lft, n.rgt, (COUNT(pn.id) - 1) AS depth FROM categories n, categories pn WHERE n.lft BETWEEN pn.lft AND pn.rgt GROUP BY n.id ORDER BY n.lft");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function sl_database_get_categories_with_product_count(PDO $connection): array
{
    $statement = $connection->query("SELECT n.id, n.name, n.lft, n.rgt, (COUNT(DISTINCT pn.id) - 1) AS depth, COUNT(DISTINCT p.id) AS products FROM categories n LEFT JOIN categories pn ON (n.lft >= pn.lft AND n.lft <= pn.rgt) LEFT JOIN products p ON (n.id = p.category_id) GROUP BY n.id ORDER BY n.lft");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function sl_database_get_statuses(PDO $connection): array
{
    $statement = $connection->query("SELECT id, name FROM order_statuses ORDER BY id");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function sl_database_get_product_by_sku(PDO $connection, string $sku): array
{
    $statement = $connection->prepare("SELECT p.id, SUBSTRING_INDEX(GROUP_CONCAT(pp.id ORDER BY pp.created DESC), ',', 1) AS product_price_id FROM products p LEFT JOIN product_prices pp ON (p.id = pp.product_id) WHERE p.sku = :sku GROUP BY p.id");
    $statement->bindValue(":sku", $sku, PDO::PARAM_STR);
    $statement->execute();

    if ($statement->rowCount() !== 1) {
        return [];
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}
