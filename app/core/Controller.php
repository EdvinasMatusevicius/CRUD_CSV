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
            if ($jsonResponse === false) {
                $jsonResponse = '{"jsonError":"unknown"}';
            }
            http_response_code(500);
        }
        echo $jsonResponse;
    }
}