<?php
session_start();
session_unset();
session_destroy();
header("Location: homepage-main-menu.php");
exit();
?>
