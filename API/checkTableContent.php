<?php
require_once "../config.php";
require_once "../HVtable.class.php";
$HVtable = new HVtable($mysqli,$_GET['table_id']);
echo $HVtable->checkTableContent();
?>