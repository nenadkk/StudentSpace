<?php

require_once "tool.php";

if (Tool::isLoggedIn()) {
    Tool::endUserSession();
}
header("Location: index.php");
exit;

?>