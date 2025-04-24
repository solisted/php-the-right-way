<?php
declare(strict_types=1);

function sl_template_escape_array(array $array): array
{
    return array_map("htmlspecialchars", $array);
}

function sl_template_escape_array_of_arrays(array $array): array
{
    return array_map(function (array $inner_array) {
            return array_map("htmlspecialchars", $inner_array);
        },
        $array
    );
}

function sl_template_render_header(): void
{
    require("../templates/header.php");
}

function sl_template_render_footer(): void
{
    require("../templates/footer.php");
}

function sl_template_render_sidebar(): void
{
    require("../templates/sidebar.php");
}

function sl_template_render_pager(string $url, int $page, int $size, int $total_pages, ?array $filter = null): void
{
    $filter_string = "";

    if ($filter !== null) {
        $filter_string = "&" . http_build_query($filter);
    }

    if ($total_pages > 1) {
        require("../templates/pager.php");
    }
}
