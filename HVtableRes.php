<?php
require_once "config.php";
require_once "HVtable.class.php";

$action = $_REQUEST['action'];
$tableId = $_REQUEST['table_id'];

$HVtable = new HVtable($mysqli,$tableId);
if($action == 'addRow')
{
    $HVtable->addRow();
    $json = array('status'=>1);
}
else if($action == 'addColumn')
{
    $HVtable->addColumn();
    $json = array('status'=>1);
}
else if($action == 'emptyHV')
{
    $HVtable->emptyHV();
    $json = array('status'=>1);
}
else if($action == 'createNew')
{
    if($_POST['template'] == "bugAssign")
    {
        $HVtable->bugAssignTemplate($_POST['table_name']);
    }
    else if($_POST['template'] == "testStrategy")
    {
        $HVtable->testStrategyTemplate($_POST['table_name']);
    }
    else{
        $HVtable->createNew($_POST['table_name'],$_POST['column_grade'],$_POST['row_grade']);
    }
    $json = array('status'=>1);
}
else if($action == 'updateColumn')
{
    $HVtable->updateColumn($_POST['column_id'],$_POST['column_name'],$_POST['column_merge']);
    $json = array('status'=>1);
}
else if($action == 'updateRow')
{
    $HVtable->updateRow($_POST['row_id'],$_POST['row_name'],$_POST['row_merge']);
    $json = array('status'=>1);
}
else if($action == 'updateValue')
{
    $HVtable->updateValue($_POST['value_id'],$_POST['value_name'],$_POST['value_postil']);
    $json = array('status'=>1);
}
else if($action == 'checkColumn')
{
    $json = $HVtable->checkColumn($_POST['column_id']);
}
else if($action == 'checkRow')
{
    $json = $HVtable->checkRow($_POST['row_id']);
}
else if($action == 'checkValue')
{
    $json = $HVtable->checkValue($_POST['value_id']);
}
else if($action == 'deleteRow')
{
    $HVtable->deleteRow($_POST['row_id']);
    $json = array('status'=>1);
}
else if($action == 'deleteColumn')
{
    $HVtable->deleteColumn($_POST['column_id'],$_POST['p_column_id'],$_POST['p_method'],$_POST['max_grade']);
    $json = array('status'=>1);
}else if($action == 'checkRemark')
{
    $json = $HVtable->checkRemark();
}
else if($action == 'updateRemark')
{
    $HVtable->updateRemark($_POST['remark']);
    $json = array('status'=>1);
}
echo json_encode($json);
?>