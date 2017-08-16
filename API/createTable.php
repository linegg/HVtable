<?php
require_once "../config.php";
require_once "../HVtable.class.php";

$tableName = $_GET['table_name'];
$template = $_GET['template'];

$HVtable = new HVtable($mysqli,0);
if($template == 'bugAssign')
{
    $tableId = $HVtable->bugAssignTemplate($tableName);

}
else if($template == 'testStrategy')
{
    $tableId = $HVtable->testStrategyTemplate($tableName);
}
echo $tableId;
?>