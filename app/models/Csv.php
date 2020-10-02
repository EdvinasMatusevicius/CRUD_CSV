<?php

class Csv extends Database{

    public function parseCsvData(array $csv){
        //SET USER SELECTED DELIMETER
        $fileName = $csv['tmp_name'];
        $file = fopen($fileName,"r");
        $data=[];
        while(($column = fgetcsv($file,10000,",")) !== FALSE){   
            array_push($data,$column);
        }
        fclose($file);
        return $data;
    }

    public function createCsvTable(string $tableName,int $ColumnCount){
        $query = $this->buildCreateQuery($tableName,$ColumnCount);
        mysqli_query($this->getDbConnection(),$query);
    }
    public function insertCsvTable(string $tableName,array $data){
        $query = $this->buildInsertQuery($tableName,$data);
        mysqli_query($this->getDbConnection(),$query);

    }
    public function deleteCsvTable(string $tableName){
        
    }
    public function updateTableList(string $newTable,?string $oldTable=null){
        if($oldTable){
            $query = "SELECT * FROM `tables_list` WHERE `new` = '$oldTable'";
        }else{
            $newFileQuery = "INSERT INTO `tables_list` (new) VALUES ('$newTable');";
            echo $newFileQuery;
            mysqli_query($this->getDbConnection(),$newFileQuery);
        }
    }
    protected function buildCreateQuery(string $tableName,int $ColumnCount){
        $columnNameArr = range('A','Z');
        $columns = '';
        for($i=0; $i<= $ColumnCount-1; $i++){
            $columns .= $columnNameArr[$i] . " VARCHAR(200)";
            $columns .= $i === $ColumnCount-1 ? "" : ",";
        }
        return "CREATE TABLE `$tableName` ($columns);";
    }
    protected function buildInsertQuery(string $tableName,array $data){
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