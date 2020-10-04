<?php
include_once 'app/core/Controller.php';
class CsvController extends Controller{
    protected $csvModel;
    public function __construct()
    {
        $this->csvModel = $this->model('Csv');
    }

    public function index (){
        try {
            $nestedArrRes = $this->csvModel->getAllFileNames();
            $listArr = [];
            foreach ($nestedArrRes as $arr) {
                array_push($listArr,$arr['new']);
            }
            echo $this->jsonResponse($listArr);
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        } 
    }
    public function create()
    {
        try {
            $csv = $_FILES['csv'];
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
            if($csv['size']>0 && in_array($csv['type'],$csvMimes) && strlen($csv['name'])<=70){
                $tableName = $csv['name'].date("Y/m/d_h:i:s");
                $data= $this->csvModel->parseCsvData($csv,$_POST['delimiter']);
                if($this->csvModel->validateColumnAmount($data)){
                    throw new Exception("Incorrect column count");
                };
                $colCount = count($data[0]);
                $this->store($tableName,$colCount,$data);
            }else{
                echo $this->jsonException('File must be csv type, can\'t be empty and file name can\'t be longer than 70 characters');
            }
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        }    
    }

    public function store(string $tableName,int $colCount,array $data)
    {
        try {
            $this->csvModel->createCsvTable($tableName,$colCount);
            $this->csvModel->insertCsvTable($tableName,$data); 
            $this->csvModel->updateTableList($tableName); 
            echo $this->jsonResponse('file stored');
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        } 
    }

    public function show()
    {

    }

    public function edit()
    {
        try {
            $data = json_decode($_POST['data'],true);
            if($this->csvModel->validateColumnAmount($data)){
                throw new Exception("Incorrect column count");
            };
            $tableName = $_POST['tableName'];
            $colCount = count($data[0]);
            $newOldTableNamesInDbList = $this->csvModel->getOldAndNewTableName($tableName);
            //validates if user gives back existing table name in db list 
            if($newOldTableNamesInDbList === NULL){
                throw new Exception("Incorrect file name");
            }
            $oldName = $newOldTableNamesInDbList['old'];
            $newName = $newOldTableNamesInDbList['new'];
            $this->update($data,$newName,$colCount,$oldName);
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        } 
    }

    public function update(array $data,string $newName,int $colCount,?string $oldName=null)
    {
        try {
            if($oldName === NULL){
                $oldName = $newName.'_old';
            }else{
                $this->csvModel->deleteCsvTable($oldName);
            }
            $this->csvModel->renameCsvTable($newName,$oldName); 
            $this->csvModel->createCsvTable($newName,$colCount);
            $this->csvModel->insertCsvTable($newName,$data); 
            $this->csvModel->updateTableList($newName,$oldName);
            echo $this->jsonResponse('Changes saved');
        } catch (Exception $exception) {
            echo $this->jsonException($exception->getMessage());
        } 
    }


    public function destroy($id)
    {
        //
    }
}