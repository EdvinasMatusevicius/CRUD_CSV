<?php
include_once 'app/core/Controller.php';
class CsvController extends Controller{

    public function index (){
        echo 'hello';
    }
    public function create()
    {
        try {
            
            //throw $th;
            $csv = $_FILES['csv'];
            if($csv['size']>0){
                $fileName = $csv['tmp_name'];
                $file = fopen($fileName,"r");
                $data=[];
                while(($column = fgetcsv($file,10000,",")) !== FALSE){
                    array_push($data,$column);
                }
                fclose($file);
                $tableName = $csv['name'].date("Y/m/d_h:i:s");
                $this->model('Csv')->createCsvTable($tableName,count($data[0]));
                
            }
        } catch (Exception $exception) {
            //create jsonException in controller
            echo $exception->getMessage();
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