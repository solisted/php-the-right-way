<?php
declare(strict_types=1);

require("../includes/template.php");

function sl_render_404(): void
{
    require("../templates/404.php");
}

sl_template_render_header();
sl_render_404();
sl_template_render_footer();
