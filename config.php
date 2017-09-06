<?php
header('Content-Type:text/html;charset = utf8');

$dbHost = '';
$dbUser = '';
$dbPwd = '';
$dbDatabase = '';

$mysqli = new mysqli($dbHost,$dbUser,$dbPwd,$dbDatabase);
if(mysqli_connect_errno()) {
    print_r("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$mysqli->set_charset("utf8");

define("NEWROW","NEW");
define("NEWCOLUMN","NEW");
define("DOWNLOAD_DIR","./files/");
?>