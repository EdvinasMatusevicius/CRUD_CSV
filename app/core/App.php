<?php

class App{
    protected $conn;
    protected $req;
    protected $controller;
    protected $err404 = false;
    function __construct()
    {
        $this->conn = (new Database)->getDbConnection();
        $this->parseReq();
        $this->setController();
        $this->callControllerMethod();
        if($this->err404){
            $this->jsonException('404. Page doesn\'t exist');
        }
    }

    private function parseReq(){
        $explodedChunks = 4; //limit on chunks because on show request url param value has /
        if($_SERVER['APP_ENV'] === 'dev'){ //for development with xampp
            $explodedChunks = 5;
        }
        $this->req = explode('/',filter_var(rtrim($_SERVER['REQUEST_URI'],'/'),FILTER_SANITIZE_URL),$explodedChunks);
    }
    private function setController(){
        $controllerIndex = 1;
        if($_SERVER['APP_ENV'] === 'dev'){ //for development with xampp
            $controllerIndex = 2;
        }
        if (array_key_exists( $controllerIndex, $this->req )){
            $this->controller = $this->req[2] . "Controller";
        }
        if(file_exists("app/controllers/". $this->controller . ".php")){
            require_once "app/controllers/". $this->controller . ".php";
        }else{
            $this->err404 = true;
        }
    }
    private function callControllerMethod(){
        $methodIndex = 2;
        if($_SERVER['APP_ENV'] === 'dev'){ //for development with xampp
            $methodIndex = 3;
        }
        if (array_key_exists( $methodIndex, $this->req )){
            $method = $this->req[$methodIndex];
        }
        if(!$this->err404 && method_exists(new $this->controller,$method)){
            (new $this->controller)->$method($this->conn);
        }else{
            $this->err404 = true;
        }
    }
    private function ifInArrayValidation($index, array $array){
        if (!array_key_exists( $index, $array )){
            $this->err404 = true;
        }
    }
    private function jsonException($message){
        header("Content-Type: application/json");
        $jsonExceptionResponse = json_encode(['error'=> $message ?? 'error occurred']);
        if ($jsonExceptionResponse === false) {
            $jsonExceptionResponse = json_encode(["jsonError" => json_last_error_msg()]);
            http_response_code(500);
        }
        http_response_code(404);
        echo $jsonExceptionResponse;
    }
}