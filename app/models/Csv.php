<?php

class Csv extends Database{

    protected $query;

    public function createCsvTable($tableName,$ColumnCount){
        $query = $this->buildCreateQuery($tableName,$ColumnCount);
        $conn = $this->getDbConnection();
        mysqli_query($conn,$query);
    }
    public function insertCsvTable($tableName,$data){
        $query = $this->buildInsertQuery($tableName,$data);
        $conn = $this->getDbConnection();
        mysqli_query($conn,$query);

    }
    protected function buildCreateQuery($tableName,$ColumnCount){
        $columnNameArr = range('A','Z');
        $columns = '';
        for($i=0; $i<= $ColumnCount-1; $i++){
            $columns .= $columnNameArr[$i] . " VARCHAR(200)";
            $columns .= $i === $ColumnCount-1 ? "" : ",";
        }
        return "CREATE TABLE `$tableName` ($columns);";
    }
    protected function buildInsertQuery($tableName,$data){
        $columnNameArr = range('A','Z');
        $columnCount = count($data[0]);
        $rowCount = count($data);
        $columnNames = implode(",",array_slice($columnNameArr,0,$columnCount));
        $query = "INSERT INTO `$tableName` ($columnNames) VALUES ";
        for ($i=0; $i <=$rowCount-1 ; $i++) { 
            $row = implode('\',\'',$data[$i]);
            $query .= "('$row')" . ($rowCount-1 !== $i ? ',':';');
        }
        return $query;
    }
}