<html>
<head>
    <meta charset="UTF-8">
    <title></title>
	<link rel="stylesheet" type="text/css" href="./gui/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="./summernote-master/dist/summernote.css"/>
    <link rel="stylesheet" type="text/css" href="./gui/css/HVtable.css"/>

    <script type="text/javascript" src="./gui/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="./gui/js/bootstrap.js"></script>
    <script type="text/javascript" src="./summernote-master/dist/summernote.min.js"></script>
    <script type="text/javascript" src="./gui/js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="./gui/js/HVtable.js"></script>
</head>
<div>
    <span><a class="help" href="http://192.168.4.56/HVtable/helpDoc/instructions.html" target="_blank">使用说明</a></span>
    <button id="addRow">增加一行</button><button id="addColumn">增加一列</button><button id="importExcelBtn">导入表格</button>
    <input type="radio" name="editMode" value="0" checked="checked">默认
    <input type="radio" name="editMode" value="1">高级
</div>
<?php
require_once "config.php";
require_once "HVtable.class.php";

$hvTable = new HVtable($mysqli,$_GET['table']);
$html = $hvTable->drawTable();

echo $html;
?>
<div class="modal fade" id="updateColumn" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id='updateColumnForm'>
                <table class="table">
                    <tr>
                        <td>内容</td>
                        <td><input class='inputStyle inputFocus' type='text' id='column_name' name='column_name'/></td>
                    </tr>
                    <tr>
                        <td>向右合并</td>
                        <td><input class='inputStyle' type='number' id='column_merge' name='column_merge'/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rightAlign">
                            <input class="subUpdate" type='submit' value='确定'/>
                            <button class="cancelUpdate" type='button'>取消</button>
                            <button class="deleteColumn" type='button' style="display:none">删除列</button>
                            <input type='hidden' id='column_id' name='column_id' value=''/>
                            <input type='hidden' id='column_table_id' name='table_id' value=''/>
                            <input type='hidden' id='trIndex' value=''/>
                            <input type='hidden' id='tdIndex' value=''/>
                            <input type='hidden' id='max_grade' value=''/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateRow" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id='updateRowForm'>
                <table class="table">
                    <tr>
                        <td>内容</td>
                        <td><input class='inputStyle inputFocus' type='text' id='row_name' name='row_name'/></td>
                    </tr>
                    <tr>
                        <td>向下合并</td>
                        <td><input class='inputStyle' type='number' id='row_merge' name='row_merge'/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rightAlign">
                            <input class="subUpdate" type='submit' value='确定'/>
                            <button class="cancelUpdate" type='button'>取消</button>
                            <button class="deleteRow" type='button' style="display:none">删除行</button>
                            <input type='hidden' id='row_id' name='row_id' value=''/>
                            <input type='hidden' id='row_table_id' name='table_id' value=''/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateValue" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id='updateValueForm'>
                <table class="table">
                    <tr>
                        <td>内容</td>
                        <td><input class='inputStyle inputFocus' type='text' id='value_name' name='value_name'/></td>
                    </tr>
                    <tr>
                        <td>批注</td>
                        <td><input class='inputStyle' type='text' id='value_postil' name='value_postil'/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rightAlign">
                            <input class="subUpdate" type='submit' value='确定'/>
                            <button class="cancelUpdate" type='button'>取消</button>
                            <input type='hidden' id='value_id' name='value_id' value=''/>
                            <input type='hidden' id='value_table_id' name='table_id' value=''/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateRemark" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id='updateRemarkForm'>
                <table class="table">
                    <tr>
                        <td>表格备注</td>
                        <td>
                            <textarea class='textStyle inputFocus' id='remark' name='remark'/></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rightAlign">
                            <input class="subUpdate" type='submit' value='确定'/>
                            <button class="cancelUpdate" type='button'>取消</button>
                            <input type='hidden' id='remark_table_id' name='table_id' value=''/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="importExcel"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-show="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id='importExcelForm'>
                <table class="table">
                    <tr>
                        <td>文件路径</td>
                        <td><input id="importFile" name="importFileName" type="file" /></td>
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
                        <td>注意</td>
                        <td>
                        	1、导入的表格将会完全覆盖当前表<br>
                        	2、源文件中表头的最低级和表格内容不能存在合并单元格<br>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rightAlign">
                            <input id='importExcelSub' type='submit' value='确定'/>
                            <button class="cancelUpdate" type='button'>取消</button>
                            <input type="hidden" id="import_table_id" name="table_id" value=""/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

</html>
