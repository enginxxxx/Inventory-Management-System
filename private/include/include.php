<?php
define("APP_ROOT", dirname(dirname(__FILE__)));
define("PRIVATE_PATH", APP_ROOT . "/");
define("DOMAIN_NAME", 'http://example.com');

require_once(PRIVATE_PATH . "/include/passwords.php");
require_once(PRIVATE_PATH . "/include/database.php");
require_once(PRIVATE_PATH . "/include/misc_functions.php");
?>