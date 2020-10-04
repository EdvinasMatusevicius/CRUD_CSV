<?php

class Csv extends Database{
    // need prepared statements for query
    public function parseCsvData(array $csv, $delimiter){
        $delimiterArr = ["comma"=>",","semicolon"=>";","pipe"=>"|"];
        $fileName = $csv['tmp_name'];
        $file = fopen($fileName,"r");
        $data=[];
        while(($column = fgetcsv($file,10000,$delimiterArr[$delimiter])) !== FALSE){   
            array_push($data,$column);
        }
        fclose($file);
        return $data;
    }

    public function getAllFileNames(){
        $query = "SELECT `new` FROM `tables_list`;";
        $result = mysqli_query($this->getDbConnection(),$query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createCsvTable(string $tableName,int $columnCount){
        $query = $this->buildCreateQuery($tableName,$columnCount);
        mysqli_query($this->getDbConnection(),$query);
    }

    public function insertCsvTable(string $tableName,array $data){
        $query = $this->buildInsertQuery($tableName,$data);
        mysqli_query($this->getDbConnection(),$query);
    }

    public function deleteCsvTable(string $tableName){
        $query = "DROP TABLE `$tableName`;";
        mysqli_query($this->getDbConnection(),$query);
    }

    public function renameCsvTable($originalName,$newName){
        $query = "ALTER TABLE `$originalName` RENAME TO `$newName`;";
        mysqli_query($this->getDbConnection(),$query);
    }
    public function updateTableList(string $newTable,?string $oldTable=null){
        if($oldTable){
            $query = "UPDATE `tables_list` SET `old`='$oldTable' WHERE `new` = '$newTable';";
            mysqli_query($this->getDbConnection(),$query);
        }else{
            $newFileQuery = "INSERT INTO `tables_list` (new) VALUES ('$newTable');";
            mysqli_query($this->getDbConnection(),$newFileQuery);
        }
    }

    public function getOldAndNewTableName($newTable){
        $query = "SELECT `old`,`new` FROM `tables_list` WHERE `new` = '$newTable';";
        $result = mysqli_query($this->getDbConnection(),$query);
        return mysqli_fetch_assoc($result);
    }

    public function validateColumnAmount($data){
        $columnsInFirstRow =count($data[0]);
        $failedValidation = false;
        if($columnsInFirstRow <= 0 || $columnsInFirstRow >= 16384){
            $failedValidation = true;
        }
        foreach ($data as $row) {
            if(count($row) !== $columnsInFirstRow){
                $failedValidation = true;
            }
        }
        return $failedValidation;
    }
    protected function buildCreateQuery(string $tableName,int $columnCount){
        $columnNameArr = range(1,$columnCount);
        $columns = '';
        for($i=0; $i<= $columnCount-1; $i++){
            $columns .= "`$columnNameArr[$i]`" . " VARCHAR(200)";
            $columns .= $i === $columnCount-1 ? "" : ",";
        }
        return "CREATE TABLE `$tableName` ($columns);";
    }

    protected function buildInsertQuery(string $tableName,array $data){
        $columnCount = count($data[0]);
        $rowCount = count($data);
        $columnNames = implode("`,`",range(1,$columnCount));
        $query = "INSERT INTO `$tableName` (`$columnNames`) VALUES ";
        for ($i=0; $i <=$rowCount-1 ; $i++) { 
            $row = implode('\',\'',$data[$i]);
            $query .= "('$row')" . ($rowCount-1 !== $i ? ',':';');
        }
        return $query;
    }
}