<?php

class Csv extends Database{

    public function parseCsvData(array $csv, $delimiter){
        $delimiterArr = ["comma"=>",","semicolon"=>";","pipe"=>"|"];
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
        echo $query;
        mysqli_query($this->getDbConnection(),$query);
    }
    public function insertCsvTable(string $tableName,array $data){
        $query = $this->buildInsertQuery($tableName,$data);
        echo $query;

        mysqli_query($this->getDbConnection(),$query);

    }
    public function deleteCsvTable(string $tableName){
        
    }
    public function updateTableList(string $newTable,?string $oldTable=null){
        if($oldTable){
            $query = "SELECT * FROM `tables_list` WHERE `new` = '$oldTable'";
        }else{
            $newFileQuery = "INSERT INTO `tables_list` (new) VALUES ('$newTable');";
            mysqli_query($this->getDbConnection(),$newFileQuery);
        }
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