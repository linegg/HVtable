<?php
require_once "config.php";
require_once "HVtable.class.php";

$filename = $_GET['filename'];
if(empty($filename))
{
    exit();
}
HVtable::downloadXls(DOWNLOAD_DIR,iconv("UTF-8","GB2312",$filename));
?>