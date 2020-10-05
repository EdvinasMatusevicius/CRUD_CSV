<?php

class Csv extends Database{
    // need prepared statements for query
    public function parseCsvData(array $csv, $delimiter){
        $delimiterArr = ["comma"=>",","semicolon"=>";","pipe"=>"|"];
        if(!$delimiterArr[$delimiter]){
            throw new Exception("Invalid delimeter");
        }
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
        if (!$result) {
            throw new Exception("No results in database");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getFileData($tableName){
        $query = "SELECT * FROM `$tableName`";
        $result = mysqli_query($this->getDbConnection(),$query);
        if (!$result) {
            throw new Exception("No results in database");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function createCsvTable(string $tableName,int $columnCount){
        $query = $this->buildCreateQuery($tableName,$columnCount);
        mysqli_query($this->getDbConnection(),$query);
    }

    public function insertCsvTable(string $tableName,array $data){
        $query = $this->buildInsertQuery($tableName,$data);
        foreach ($data as $row) {
            $query->bind_param(str_repeat("s", count($row)), ...$row);
            $query->execute();
        }
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
            $query = "UPDATE `tables_list` SET `old`=? WHERE `new`=? ;";
            $stmt = $this->getDbConnection()->prepare($query);
            $stmt->bind_param('ss',$oldTable,$newTable);
            $stmt->execute();
        }else{
            $newFileQuery = "INSERT INTO `tables_list` (new) VALUES (?);";
            $stmt = $this->getDbConnection()->prepare($newFileQuery);
            $stmt->bind_param('s',$newTable);
            $stmt->execute();
        }
    }

    public function getOldAndNewTableName($newTable){
        $query = "SELECT `old`,`new` FROM `tables_list` WHERE `new` = '$newTable';";
        $result = mysqli_query($this->getDbConnection(),$query);
        if (!$result) {
            throw new Exception("No results in database");
        }
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
        $columnNames = implode("`,`",range(1,$columnCount));
        $params = implode(",", array_fill(0, $columnCount, "?"));
        $query = "INSERT INTO `$tableName` (`$columnNames`) VALUES ($params)";
        return $this->getDbConnection()->prepare($query);
    }
}