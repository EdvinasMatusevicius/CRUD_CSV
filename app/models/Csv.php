<?php

class Csv extends Database{

    protected $query;

    public function createCsvTable($tableName,$ColumnCount){
        $query = $this->buildCreateQuery($tableName,$ColumnCount);
        $conn = $this->getDbConnection();
        mysqli_query($conn,$query);
    }
    public function insertCsvTable(){

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
}