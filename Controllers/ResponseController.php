<?php

namespace Controllers;

class ResponseController
{
    public $response = [
        'http_status' => 200,
        'http_message' => "OK.",
        'response_data' => [],
        'request_data' => [],
        'sql' => [],
        'request_processing_time' => 0
    ];

    public function send()
    {
        echo json_encode($this->response);
    }

    public static function readRequestData()
    {
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($content_type, 'application/json') === false) {
            throw new Exception('Content-Type must be application/json');
        }

        // Read the input stream
        $body = file_get_contents("php://input");

        // Decode the JSON object
        return json_decode($body, true);
    }


    public function httpStatus($int = 200)
    {
        $this->response['http_status'] = $int;
    }

    public function setMessage($string = '')
    {
        $this->response['http_message'] = $string;
    }


    public function requestData($data = [])
    {
        $this->response['request_data'] = $data;
    }


    public function responseData($data = [])
    {
        $this->response['response_data'] = $data;
    }

    public function sqlQueries($data = [])
    {
        $this->response['sql'] = $data;
    }

    public function handleMySQLResult($mysql)
    {
        $this->sqlQueries($mysql->sql);

        if($mysql->hasErrors()) {
            $this->httpStatus(500);

            $this->setMessage('Error!');
            $this->responseData($mysql->getErrors());

            http_response_code(500);
        } else {
            $this->httpStatus(200);
            http_response_code(200);

            $this->responseData($mysql->result);
        }
    }



}
