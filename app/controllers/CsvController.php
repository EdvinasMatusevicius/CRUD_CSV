<?php
include_once 'app/core/Controller.php';
class CsvController extends Controller{

    public function index (){
        echo 'hello';
    }
    public function create()
    {
        try {
            $csv = $_FILES['csv'];
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

            if($csv['size']>0 && in_array($csv['type'],$csvMimes) && strlen($csv['name'])<=70){
                $tableName = $csv['name'].date("Y/m/d_h:i:s");
                $csvModel = $this->model('Csv');
                $data= $csvModel->parseCsvData($csv);
                $csvModel->createCsvTable($tableName,count($data[0]));
                $csvModel->insertCsvTable($tableName,$data); 
                $csvModel->updateTableList($tableName); 

            }else{
                echo $this->jsonException('file must be csv type and file name can\'t be longer than 70 characters');
            }
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        }    
    }

    public function store()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update($id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}