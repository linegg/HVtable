<?php
Class HVtable
{
    public $tableId;
    public $mysqli;

    public $tableRow = array();
    public $tableColumn = array();

    public $columnSum = 0;
    public $rowSum = 0;

    public $rcValue = array();

    public function __construct($mysqli,$tableId = 0)
    {
        $this->mysqli = $mysqli;
        $this->tableId = $tableId;
    }
	//绘制表格
    public function drawTable()
    {
        $this->getColumn();
        $this->getRow();
        $this->getRcValue();

        $rowC = $this->getRowC();
        $tableInfo = $this->getTabName();
        $tabHTML = "<table class='hvTable' id='{$this->tableId}' border='1'>";
        $tabHTML .= "<tr>
                        <th class='tableNameStyle' colspan='{$this->columnSum}'>{$tableInfo['table_name']}</th>
                      </tr>";
        $this->drawTabHead($tabHTML);

        $current_merge_2 = 100;
        $current_merge_3 = 100;
        $old_merge_2 = ' ';
        $old_merge_3 = ' ';

        foreach ($this->tableRow as $rowId => $rowInfo)
        {
            $i = 0;
            $tabHTML .= "<tr>";
            foreach($this->tableColumn as $columnId=>$columnInfo)
            {
                if($i < $rowC)
                {
                    $grade = $rowC - $i;
                    if($grade > 0)
                    {
                        $rowName = "row_name_{$grade}";
                        $merge = "merge_{$grade}";

                        if($grade == 3 && $current_merge_3 < $old_merge_3)
                        {
                            $tabHTML .= "<td class='rowStyle displayNone' id='{$rowId}-m-{$grade}' rowspan='{$this->tableRow[$rowId][$merge]}'>{$this->tableRow[$rowId][$rowName]}</td>";
                            $current_merge_3 ++;
                            $i ++;
                            continue;
                        }
                        if($grade == 2 && $current_merge_2 < $old_merge_2)
                        {
                            $tabHTML .= "<td class='rowStyle displayNone' id='{$rowId}-m-{$grade}' rowspan='{$this->tableRow[$rowId][$merge]}'>{$this->tableRow[$rowId][$rowName]}</td>";
                            $current_merge_2 ++;
                            $i ++;
                            continue;
                        }
                        $tabHTML .= "<td class='rowStyle' id='{$rowId}-m-{$grade}' rowspan='{$this->tableRow[$rowId][$merge]}'>{$this->tableRow[$rowId][$rowName]}</td>";
                        if($this->tableRow[$rowId][$merge] > 1)
                        {
                            switch($grade)
                            {
                                case 2:
                                    $old_merge_2 = $this->tableRow[$rowId][$merge];
                                    $current_merge_2 = 1;
                                    break;
                                case 3:
                                    $old_merge_3 = $this->tableRow[$rowId][$merge];
                                    $current_merge_3 = 1;
                                    break;
                            }
                        }
                    }
                }
                else
                {
                    if(empty($this->rcValue[$columnId][$rowId]['value'])) $this->rcValue[$columnId][$rowId]['value'] = '';
                    if(!empty($this->rcValue[$columnId][$rowId]['postil'])){
                        $tabHTML .= "<td class='valueStyle isPostil' id='{$columnId}-{$rowId}'><a href='#' title='{$this->rcValue[$columnId][$rowId]['postil']}'>{$this->rcValue[$columnId][$rowId]['value']}</a></td>";
                    }else{
                        $tabHTML .= "<td class='valueStyle' id='{$columnId}-{$rowId}'>{$this->rcValue[$columnId][$rowId]['value']}</td>";
                    }
                }
                $i ++;
            }
            $tabHTML .= "</tr>";
        }
        $tabHTML .= "<tr><td class='remarkStyle' colspan='{$this->columnSum}'><pre>{$tableInfo['notes']}</pre></td></tr></table>";
        return $tabHTML;
    }
	
    public function columnRuler(&$tabHTML)
    {
        $tabHTML .= "<tr>";
        for($i = 0;$i < $this->columnSum;$i ++)
        {
            $tabHTML .= "<td>C</td>";
        }
        $tabHTML .= "</tr>";
    }
	//表头
    public function drawTabHead(&$tabHTML)
    {
        $columnR = $this->getColumnR();
        $old_column_merge = 0;
        $current_merge = 100;

        switch($columnR)
        {
            case 3:
                $columnArray = $this->getColumnI(3);
                $tabHTML .= "<tr>";

                foreach($columnArray as $columnId=>$columnInfo)
                {
                    if($old_column_merge >= $current_merge)
                    {
                        $current_merge ++;
                        continue;
                    }
                    $tabHTML .= "<th class='columnStyle' id='{$columnId}' colspan='{$columnInfo['merge']}'>{$columnInfo['column_name']}</th>";
                    if($columnInfo['merge'] > 1)
                    {
                        $old_column_merge = $columnInfo['merge'];
                        $current_merge = 1;
                    }
                    $current_merge ++;
                }
                $tabHTML .= "</tr>";
            case 2:
                $columnArray = $this->getColumnI(2);
                $tabHTML .= "<tr>";
                $old_column_merge = 0;
                $current_merge = 100;

                foreach($columnArray as $columnId=>$columnInfo)
                {
                    if($old_column_merge >= $current_merge)
                    {
                        $current_merge ++;
                        continue;
                    }
                    $tabHTML .= "<th class='columnStyle' id='{$columnId}' colspan='{$columnInfo['merge']}'>{$columnInfo['column_name']}</th>";
                    if($columnInfo['merge'] > 1)
                    {
                        $old_column_merge = $columnInfo['merge'];
                        $current_merge = 1;
                    }
                    $current_merge ++;
                }
                $tabHTML .= "</tr>";
            case 1:
                $columnArray = $this->getColumnI(1);
                $tabHTML .= "<tr>";
                foreach($columnArray as $columnId=>$columnInfo)
                {
                    $tabHTML .= "<th class='columnStyle' id='{$columnId}' colspan='{$columnInfo['merge']}'>{$columnInfo['column_name']}</th>";
                }
                $tabHTML .= "</tr>";
        }
    }
	
    public function getColumnI($grade)
    {
        $tableColumn = array();

        $sql = "SELECT * FROM column_table WHERE table_id={$this->tableId} AND grade={$grade}";
        $mysqlRes = $this->mysqli->query($sql);
        while($res = $mysqlRes->fetch_assoc())
        {
            $tableColumn[$res['column_id']]['column_name'] =$res['column_name'];
            $tableColumn[$res['column_id']]['merge'] =$res['merge'];
        }

        return $tableColumn;
    }
	//获取表格的列
    public function getColumn()
    {
        $sql = "SELECT * FROM column_table WHERE table_id={$this->tableId} AND grade=1";
        $mysqlRes = $this->mysqli->query($sql);
        while($res = $mysqlRes->fetch_assoc())
        {
            $this->tableColumn[$res['column_id']]['column_name'] =$res['column_name'];
            $this->tableColumn[$res['column_id']]['merge'] =$res['merge'];

            $this->columnSum += $res['merge'];
        }
    }
    //获取表格的行
    public function getRow()
    {
        $sql = "SELECT * FROM row_table WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        while($res = $mysqlRes->fetch_assoc())
        {
            $this->tableRow[$res['row_id']]['row_name_1'] = $res['row_name_1'];
            $this->tableRow[$res['row_id']]['row_name_2'] = $res['row_name_2'];
            $this->tableRow[$res['row_id']]['row_name_3'] = $res['row_name_3'];

            $this->tableRow[$res['row_id']]['merge_1'] = $res['merge_1'];
            $this->tableRow[$res['row_id']]['merge_2'] = $res['merge_2'];
            $this->tableRow[$res['row_id']]['merge_3'] = $res['merge_3'];

            $this->rowSum += $res['merge_1'];
        }
    }

    public function getTabName()
    {
        $tableInfo = array();

        $sql = "SELECT * FROM table_info WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        $tableInfo['table_name'] = $res['table_name'];
        $tableInfo['notes'] = $res['notes'];

        return $tableInfo;
    }
	//获取单元格的值
    public function getRcValue()
    {
        $sql = "SELECT * FROM row_column_value WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        while($res = $mysqlRes->fetch_assoc())
        {
            $this->rcValue[$res['column_id']][$res['row_id']]['value'] = $res['value'];
            $this->rcValue[$res['column_id']][$res['row_id']]['postil'] = $res['postil'];
        }
    }

    //获取行表头的列数
    public function getRowC()
    {
        $sql = "SELECT MAX(grade) FROM row_table WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        return $res["MAX(grade)"];
    }

    //获取列表头的行数
    public function getColumnR()
    {
        $sql = "SELECT MAX(grade) FROM column_table WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        return $res['MAX(grade)'];
    }

    public function getHVtableLists()
    {
        $tableLists = array();

        $sql = "SELECT * FROM table_info";
        $mysqlRes = $this->mysqli->query($sql);
        while($res = $mysqlRes->fetch_assoc())
        {
            $tableLists[$res['table_id']]['table_name'] = $res['table_name'];
        }

        return $tableLists;
    }
    //增加一行
    public function addRow()
    {
        $RowC = $this->getRowC();
        $sql = "INSERT INTO row_table (table_id,row_name_1,row_name_2,row_name_3,merge_1,merge_2,merge_3,grade)
                VALUES ({$this->tableId},'".NEWROW."','".NEWROW."','".NEWROW."',1,1,1,{$RowC})";
        $this->mysqli->query($sql);
    }

    //增加一列
    public function addColumn()
    {
        $columnR = $this->getColumnR();
        switch($columnR)
        {
            case 3:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                        ({$this->tableId},'".NEWCOLUMN."',1,3)";
                $this->mysqli->query($sql);
            case 2:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                        ({$this->tableId},'".NEWCOLUMN."',1,2)";
                $this->mysqli->query($sql);
            case 1:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                        ({$this->tableId},'".NEWCOLUMN."',1,1)";
                $this->mysqli->query($sql);
        }
    }

    //清空所有内容
    public function emptyHV()
    {
        $sql = "TRUNCATE table row_table";
        $this->mysqli->query($sql);

        $sql = "TRUNCATE table column_table";
        $this->mysqli->query($sql);

        $sql = "TRUNCATE table row_column_value";
        $this->mysqli->query($sql);

        $sql = "TRUNCATE table table_info";
        $this->mysqli->query($sql);
    }

    public function getMaxTab()
    {
        $sql = "SELECT MAX(table_id) FROM table_info";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        return $res['MAX(table_id)'];
    }

    //创建一个新表,表名、顶部表头行数、左侧表头列数
    public function createNew($tableName,$columnR = 1,$rowC = 1)
    {
        $sql = "INSERT INTO table_info (table_name) VALUES ('{$tableName}')";
        $this->mysqli->query($sql);

        $tableId = $this->getMaxTab();

        $sql = "INSERT INTO row_table (table_id,row_name_1,row_name_2,row_name_3,
                merge_1,merge_2,merge_3,grade) VALUES ({$tableId},'".NEWROW."','"
                .NEWROW."','".NEWROW."',1,1,1,{$rowC})";
        $this->mysqli->query($sql);

        switch($columnR)
        {
            case 3:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                ({$tableId},'".NEWCOLUMN."',1,3)";
                $this->mysqli->query($sql);
            case 2:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                ({$tableId},'".NEWCOLUMN."',1,2)";
                $this->mysqli->query($sql);
            case 1:
                $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                ({$tableId},'".NEWCOLUMN."',1,1)";
                $this->mysqli->query($sql);
        }
        return $tableId;
    }
    //更新一个column的值
    public function updateColumn($columnId,$columnName,$columnMerge)
    {
        $sql = "UPDATE column_table SET column_name='{$columnName}',merge={$columnMerge} WHERE column_id={$columnId} AND table_id={$this->tableId}";
        $this->mysqli->query($sql);
    }

    public function updateRow($rowId,$rowName,$rowMerge)
    {
        $rowInfo = explode('-m-',$rowId);
        $rowId = $rowInfo[0];
        $rowMergeL = $rowInfo[1];
        $rowN = "row_name_{$rowMergeL}";
        $rowM = "merge_{$rowMergeL}";

        $sql = "UPDATE row_table SET {$rowN}='{$rowName}',{$rowM}={$rowMerge} WHERE row_id={$rowId} AND table_id={$this->tableId}";
        $this->mysqli->query($sql);
    }

    public function updateValue($valueId,$value,$postil)
    {
        $rcInfo = explode('-',$valueId);
        $columnId = $rcInfo[0];
        $rowId = $rcInfo[1];

        $sql = "INSERT INTO row_column_value (row_id,column_id,value,postil,table_id) VALUES ({$rowId},{$columnId},'{$value}','{$postil}',{$this->tableId})
                ON DUPLICATE KEY UPDATE value='{$value}',postil='{$postil}'";
        $this->mysqli->query($sql);
    }

    public function updateRemark($remark)
    {
        $sql = "UPDATE table_info SET notes='{$remark}' WHERE table_id={$this->tableId}";
        $this->mysqli->query($sql);
    }

    public function updateTableName($tableName)
    {
        $sql = "UPDATE table_info SET table_name='{$tableName}' WHERE table_id={$this->tableId}";
        $this->mysqli->query($sql);
    }

    public function checkColumn($columnId)
    {
        $sql = "SELECT * FROM column_table WHERE column_id={$columnId} AND table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $columnInfo = $mysqlRes->fetch_assoc();
        $columnInfo['max_grade'] = $this->getColumnR();
        return $columnInfo;
    }

    public function checkRow($rowId)
    {
        $rowInfo = array();
        $rowIdInfo = explode('-m-',$rowId);
        $rowId = $rowIdInfo[0];
        $mergeL = $rowIdInfo[1];

        $mergeN = "row_name_{$mergeL}";
        $mergeM = "merge_{$mergeL}";

        $sql = "SELECT {$mergeN},{$mergeM} FROM row_table WHERE row_id={$rowId} AND table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        $rowInfo['mergeL'] = $mergeL;
        $rowInfo['merge'] = $res[$mergeM];
        $rowInfo['row_name'] = $res[$mergeN];

        return $rowInfo;
    }

    public function checkValue($valueId)
    {
        $valueIdInfo = explode('-',$valueId);
        $columnId = $valueIdInfo[0];
        $rowId = $valueIdInfo[1];

        $sql = "SELECT value,postil FROM row_column_value WHERE row_id={$rowId} AND column_id={$columnId} AND table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $valueInfo = $mysqlRes->fetch_assoc();

        return $valueInfo;
    }

    public function checkRemark()
    {
        $sql = "SELECT notes FROM table_info WHERE table_id={$this->tableId}";
        $mysqlRes = $this->mysqli->query($sql);
        $res = $mysqlRes->fetch_assoc();

        return $res;
    }

    public function deleteRow($rowId)
    {
        $rowIdInfo = explode('-m-',$rowId);
        $rowId = $rowIdInfo[0];

        $sql = "DELETE FROM row_table WHERE row_id={$rowId} AND table_id={$this->tableId}";
        $this->mysqli->query($sql);

        $sql = "DELETE FROM row_column_value WHERE row_id={$rowId} AND table_id={$this->tableId}";
        $this->mysqli->query($sql);
    }
	
    //$pMethod如果为1，则列节点的上一级merge-1；
    public function deleteColumn($columnId,$pColumnId,$pMethod,$maxGrade)
    {
        $sql = "DELETE FROM column_table WHERE column_id={$columnId} AND table_id={$this->tableId}";
        $this->mysqli->query($sql);
        if($maxGrade > 1)
        {
            $columnId2 = $columnId - 1;
			
            if($pMethod){
                $sql = "UPDATE column_table SET merge=merge-1 WHERE column_id={$pColumnId} AND table_id={$this->tableId}";
                $this->mysqli->query($sql);
                $sql = "DELETE FROM column_table WHERE column_id={$columnId2} AND table_id={$this->tableId}";
                $this->mysqli->query($sql);
            }else{
                $sql = "DELETE FROM column_table WHERE column_id={$pColumnId} AND table_id={$this->tableId}";
                $this->mysqli->query($sql);
            }

            $sql = "DELETE FROM row_column_value WHERE column_id={$columnId} AND table_id={$this->tableId}";
            $this->mysqli->query($sql);
        }
    }

    public function bugAssignTemplate($tableName)
    {
        $sql = "INSERT INTO table_info (table_name) VALUES ('{$tableName}')";
        $this->mysqli->query($sql);

        $tableId = $this->getMaxTab();

        $this->insertColumn($tableId,1,'BUG指派');
        $this->insertColumn($tableId,1,'测试员(右)<br>BUG模块(下)');
        $this->insertColumn($tableId,1,'测试人员01');
        $this->insertColumn($tableId,1,'测试人员02');
        $this->insertColumn($tableId,1,'测试人员03');

        $this->insertRow($tableId,2,'模块','大模块');
        $this->insertRow($tableId,2,'模块','大模块');
        $this->insertRow($tableId,2,'模块','大模块');
        $this->insertRow($tableId,2,'模块','大模块');
        $this->insertRow($tableId,2,'模块','大模块');

        return $tableId;
    }

    public function testStrategyTemplate($tableName)
    {
        $remark = "<p><a href=\"http://192.168.4.56/HVtable/download.php?filename=测试策略模板.xls\">测试策略模板(点击下载)</a><br/><a href=\"http://192.168.4.56/HVtable/download.php?filename=测试策略案例.xls\">测试策略案例(点击下载)</a></p>";
    	
    	$sql = "INSERT INTO table_info (table_name,notes) VALUES ('{$tableName}','{$remark}')";
        $this->mysqli->query($sql);

        $tableId = $this->getMaxTab();
        $this->insertColumn($tableId,2,'');
        $this->insertColumn($tableId,1,'版本更新内容');

        $this->insertColumn($tableId,2,'影响模块功能',2);
        $this->insertColumn($tableId,1,'关联性分析');

        $this->insertColumn($tableId,2,'影响模块功能',1);
        $this->insertColumn($tableId,1,'关联模块');

        $this->insertColumn($tableId,2,'版本测试内容',3);
        $this->insertColumn($tableId,1,'测试等级分析');

        $this->insertColumn($tableId,2,'版本测试内容',1);
        $this->insertColumn($tableId,1,'测试范围');

        $this->insertColumn($tableId,2,'版本测试内容',1);
        $this->insertColumn($tableId,1,'测试环境');

        $this->insertColumn($tableId,2,'用例是否覆盖策略',2);
        $this->insertColumn($tableId,1,'一轮');

        $this->insertColumn($tableId,2,'用例是否覆盖策略',1);
        $this->insertColumn($tableId,1,'二轮');

        $this->insertRow($tableId,1,'系统基础保障');
        $this->insertRow($tableId,1,'版本更新点-1');
        $this->insertRow($tableId,1,'版本更新点-2');
        $this->insertRow($tableId,1,'版本更新点-3');

        return $tableId;
    }

    public function insertColumn($tableId,$grade,$columnName,$merge = 1)
    {
        if(empty($tableId)){
            $tableId = $this->tableId;
        }
        if(empty($merge)){
        	$merge = 1;
        }
        $sql = "INSERT INTO column_table (table_id,column_name,merge,grade) VALUES
                ({$tableId},'${columnName}',{$merge},{$grade})";
        $this->mysqli->query($sql);
    }

    public function insertRow($tableId,$grade,$row1 = NEWROW,$row2 = NEWROW,$row3 = NEWROW,$merge1 = 1,$merge2 = 1,$merge3 = 1)
    {
        if(empty($tableId)){
            $tableId = $this->tableId;
        }
        $merge1 = empty($merge1)?1:$merge1;
        $merge2 = empty($merge2)?1:$merge2;
        $merge3 = empty($merge3)?1:$merge3;
        
        $sql = "INSERT INTO row_table (table_id,row_name_1,row_name_2,row_name_3,
                merge_1,merge_2,merge_3,grade) VALUES ({$tableId},'{$row1}','{$row2}',
                '{$row3}',{$merge1},{$merge2},{$merge3},{$grade})
                ON DUPLICATE KEY UPDATE row_name_1='{$row1}',row_name_2='{$row2}',
                row_name_3='{$row3}'";
        $this->mysqli->query($sql);
    }

    public function deleteTable($flag = true)
    {
        $sql = "DELETE FROM column_table WHERE table_id={$this->tableId}";
        $this->mysqli->query($sql);

        $sql = "DELETE FROM row_table WHERE table_id={$this->tableId}";
        $this->mysqli->query($sql);

        $sql = "DELETE FROM row_column_value WHERE table_id={$this->tableId}";
        $this->mysqli->query($sql);

        if($flag){
            $sql = "DELETE FROM table_info WHERE table_id={$this->tableId}";
            $this->mysqli->query($sql);
        }
    }

    public static function downloadXls($dir,$filename){
        $dir = chop($dir);
        $filepath = $dir.$filename;
        $filesize = filesize($filepath);
        header("Content-Type:application");
        header("Accept-Ranges:bytes");
        header("Accept-Length:".$filesize);
        header("Content-Disposition:attachment;filename=".$filename);

        $fp = fopen($filepath,"r");
        $buffer_size = 1024;
        $cur_pos = 0;

        while(!feof($fp) && $filesize - $cur_pos > $buffer_size)
        {
            $buffer = fread($fp,$buffer_size);
            echo $buffer;
            $cur_pos += $buffer_size;
        }

        $buffer = fread($fp,$filesize - $cur_pos);
        echo $buffer;
        fclose($fp);
        return true;
    }
    //调整列表头的ID,不调整的话合并单元格可能会出问题
    public function formatColumnId()
    {
    	$sql = "UPDATE column_table SET column_id=column_id+100 WHERE table_id={$this->tableId}";
    	$this->mysqli->query($sql);
    	
    	$sql = "SELECT count(*) FROM column_table WHERE table_id={$this->tableId} AND grade=1";
    	$mysqlRes = $this->mysqli->query($sql);
    	$maxColumn = $mysqlRes->fetch_assoc()['count(*)'];
    	
    	$sql = "UPDATE column_table SET column_id=(column_id-100)*2-1 WHERE table_id={$this->tableId} AND grade=2";
    	$this->mysqli->query($sql);
    	
    	$sql = "UPDATE column_table SET column_id=(column_id-100-{$maxColumn})*2 WHERE table_id={$this->tableId} AND grade=1";
    	$this->mysqli->query($sql);
    	
    	$sql = "UPDATE row_column_value SET column_id=(column_id-{$maxColumn})*2 WHERE table_id={$this->tableId}";
    	$this->mysqli->query($sql);
    }
    
    public function checkTableContent()
    {
    	$sql = "SELECT value FROM row_column_value WHERE table_id={$this->tableId} AND value!=''";
    	$mysqlRes = $this->mysqli->query($sql);
    	$numRows = $mysqlRes->num_rows;
    	
    	if($numRows == 0){
    		return false;
    	}
    	return true;
    }
}
?>