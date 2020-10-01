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
        $this->req = explode('/',filter_var(rtrim($_SERVER['REQUEST_URI'],'/'),FILTER_SANITIZE_URL));
    }
    private function setController(){
        $controllerIndex = 1;
        if($_SERVER['APP_ENV'] === 'dev'){
            $controllerIndex = 2;
        }
        $this->controller = $this->req[2] . "Controller";
        require_once "app/controllers/". $this->controller . ".php";
    }
    private function callControllerMethod(){
        $methodIndex = 2;
        if($_SERVER['APP_ENV'] === 'dev'){
            $methodIndex = 3;
        }
        $method = $this->req[$methodIndex];
        (new $this->controller)->$method();
    }
}