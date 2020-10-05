<?php

class Controller{
    
    public function model($model){
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }
    public function jsonResponse($data){
        header("Content-Type: application/json");
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Requested-With');
        $jsonResponse = json_encode($data);
        if ($jsonResponse === false) {
            $jsonResponse = json_encode(["jsonError" => json_last_error_msg()]);
            http_response_code(500);
        }
        http_response_code(200);
        echo $jsonResponse;
    }
    public function jsonException($message){
        header("Content-Type: application/json");
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Requested-With'); 


        $jsonExceptionResponse = json_encode(['error'=> $message ?? 'error occurred']);
        if ($jsonExceptionResponse === false) {
            $jsonExceptionResponse = json_encode(["jsonError" => json_last_error_msg()]);
            http_response_code(500);
        }
        http_response_code(400);
        echo $jsonExceptionResponse;
    }
}