<html>
<head>
    <title>HVtable</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="./gui/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./gui/css/HVtable.css">
</head>
<div>
<?php
require_once "config.php";
require_once "HVtable.class.php";

$HVtable = new HVtable($mysqli);
$tableLists = $HVtable->GetHVtableLists();

$html = "";
foreach($tableLists as $tableId=>$tableInfo)
{
    $html .= "<button class='tabBtn btn btn-primary' id='{$tableId}'>{$tableInfo['table_name']}</button>";
}
$html .= "<button id='createTabBtn' class='btn btn-success'>创建新表格</button>";
//<button id='empty' disabled='disabled'>empty</button>
echo $html;
?>
</div>
<div >
</div>
<div class="modal fade" id="createTab" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createNewForm">
                <table class='table'>
                    <tr>
                        <td>表名称</td>
                        <td><input class="inputFocus" type="text" id="table_name" name="table_name"/></td>
                    </tr>
                    <tr>
                        <td>左侧表头</td>
                        <td><input type="number" id="row_grade" name="row_grade"/>&nbsp*1~3</td>
                    </tr>
                    <tr>
                        <td>顶部表头</td>
                        <td><input type="number" id="column_grade" name="column_grade"/>&nbsp*1~2</td>
                    </tr>
                    <tr>
                        <td>通过模板创建</td>
                        <td>
                            <input type="radio" class="templateCreate" name="templateUse" value="noUse" checked="checked">不使用模板&nbsp&nbsp&nbsp&nbsp
                            <input type="radio" class="templateCreate" name="templateUse" value="bugAssign">BUG指派表&nbsp&nbsp&nbsp&nbsp
                            <input type="radio" class="templateCreate" name="templateUse" value="testStrategy">测试策略表
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" value="创建"/>
                            <button type="button" id="cancelCreate">取消</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="./gui/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="./gui/js/bootstrap.js"></script>
<script type="text/javascript" src="./gui/js/HVtable.js"></script>
</html>
