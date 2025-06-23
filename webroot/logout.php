<?php
declare(strict_types=1);

require("../includes/errors.php");
require("../config/config.php");
require("../includes/authentication.php");
require("../includes/request.php");

sl_request_method_assert("GET");

session_destroy();

sl_request_redirect("/login");
