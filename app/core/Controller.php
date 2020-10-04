<?php

class Controller{
    
    public function model($model){
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }
    public function jsonResponse($data){
        header("Content-Type: application/json");
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
        $jsonExceptionResponse = json_encode(['error'=> $message ?? 'error occurred']);
        if ($jsonExceptionResponse === false) {
            $jsonExceptionResponse = json_encode(["jsonError" => json_last_error_msg()]);
            http_response_code(500);
        }
        http_response_code(400);
        echo $jsonExceptionResponse;
    }
}