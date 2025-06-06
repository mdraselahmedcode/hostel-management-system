<?php

    function json_response($data , $statusCode = 200):void {
        http_response_code($statusCode); 
        header("Content-Type: application/json; charset=utf-8"); 
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit; 
    }


    // function json_response($message, $success = true, $statusCode = 200): void {
    // http_response_code($statusCode);
    // header("Content-Type: application/json; charset=utf-8");
    // echo json_encode([
    //     'success' => $success,
    //     'message' => $message
    // ], JSON_PRETTY_PRINT);
    // exit;
    // }


?>