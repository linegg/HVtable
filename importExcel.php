<?php
error_reporting(0);
require "config.php";
require "PHPExcel.php";
require  "./PHPExcel/Writer/Excel5.php";
require "HVtable.class.php";

$xlsName = $_FILES['importFileName']['name'];
$xlsName = str_replace('.xls','',$xlsName);

$rowGrade = $_GET['row_grade'];
$columnGrade = $_GET['column_grade'];

$hvTable = new HVtable($mysqli,$_GET['table_id']);
$hvTable->deleteTable(false);
$hvTable->updateTableName($xlsName);

$filePath = $_FILES['importFileName']['tmp_name'];
if($filePath == "")
{
	exit();
}

$objExcel = new PHPExcel();
$objReader = new PHPExcel_Reader_Excel5();
$objExcel = $objReader->load($filePath);
$objSheet = $objExcel->getSheet(0);

$maxRow = $objSheet->getHighestRow();
$maxColumn = $objSheet->getHighestColumn();
$maxColumnNum = ord($maxColumn)-64;

$mergeInfo = checkMergeCells($objSheet);

for($currentRow = 1;$currentRow <= $maxRow;$currentRow ++)
{
    $row1 = "";
    $row2 = "";
    $row3 = "";

    for($currentColumn = 'A';$currentColumn <= $maxColumn;$currentColumn ++)
    {
        $cell = $currentColumn.$currentRow;
    	$val = htmlspecialchars($objSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue());
        if($currentRow <= $columnGrade)
        {
        	$currentColumnGrade = $columnGrade - $currentRow + 1;
            $hvTable->insertColumn(null,$currentColumnGrade,$val,$mergeInfo[$cell]['c']);
        }else{
            $currentColumnNum = ord($currentColumn) - 65;
            if($currentColumnNum < $rowGrade)
            {
                $valLevel = $rowGrade - $currentColumnNum;
                $vkey = "row{$valLevel}";
                $vmerge = "merge{$valLevel}";
                
                $$vkey = $val;
                $$vmerge = $mergeInfo[$cell]['r'];
            }else{
                $valueId = ($currentColumnNum + 1 + $maxColumnNum * ($columnGrade - 1)).'-'.($currentRow - $columnGrade);
                $hvTable->updateValue($valueId,$val,'');
            }
        }
    }
    if($currentRow <= $columnGrade)continue;
    $hvTable->insertRow(null,$rowGrade,$row1,$row2,$row3,$merge1,$merge2,$merge3);
}
if($columnGrade > 1)
{
	$hvTable->formatColumnId();
}

$json = array("data"=>1,"status"=>1);
echo json_encode($json);

function checkMergeCells($objSheet)
{
	$mergeArray = $objSheet->getMergeCells();
	$mergeInfo = array();
	foreach($mergeArray as $k => $mergeCells)
	{
		$cells = explode(':',$mergeCells);
		$startCell = $cells[0];
		$endCell = $cells[1];
		
		$mergeInfo[$startCell] = isValidMergeCells($startCell, $endCell); 
	}
	
	return $mergeInfo;
}

function isValidMergeCells($startCell,$endCell)
{
	$mergeInfo = array('c'=>0,'r'=>0);
	
	$sNum = preg_replace('/^[A-Z]/','',$startCell);
	$eNum = preg_replace('/^[A-Z]/','',$endCell);
	
	$sWord = preg_replace('/\d+/','',$startCell);
	$eWord = preg_replace('/\d+/','',$endCell);
	
	if($sWord == $eWord)
	{
		$mergeInfo['r'] = $eNum - $sNum + 1;
	}else if($sNum == $eNum){
		$mergeInfo['c'] = ord($eWord) - ord($sWord) + 1;
	}
	return $mergeInfo;
}

?>