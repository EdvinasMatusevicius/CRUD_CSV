<?php

class App{
    protected $conn;
    protected $req;
    protected $controller;
    function __construct()
    {
        $this->conn = (new Database)->getDbConnection();
        $this->parseReq();
        $this->setController();
        $this->callControllerMethod();
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
        $this->controller = $this->req[2] . "Controller";
        require_once "app/controllers/". $this->controller . ".php";
    }
    private function callControllerMethod(){
        $methodIndex = 2;
        if($_SERVER['APP_ENV'] === 'dev'){ //for development with xampp
            $methodIndex = 3;
        }
        $method = $this->req[$methodIndex];
        (new $this->controller)->$method($this->conn);
    }
}