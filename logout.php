<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: dagplanning.php");
exit;
