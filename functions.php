<?php

use Controllers\MySQLController;
use Controllers\ResponseController;
use Controllers\BackupController;

function env($key, $default = null)
{
    if(!array_key_exists($key, $_ENV)) {
        return null;
    }

    $value = $_ENV[$key];

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return;
    }
/*
    if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
        return substr($value, 1, -1);
    }
*/
    return $value;
}



function info($vars)
{
    $mysql = new MySQLController();
    $response = new ResponseController();

    //$mysql->getSingleItem('3b093c40-2ab6-4b18-8549-63524aac0d16');
    //$mysql->getBranch('8c2001c6-58a5-4d3f-b5c3-b750ddcdf98a', true);
    $mysql->getBranch(null, true);

    $response->handleMySQLResult($mysql);
    $response->send();
}

function getAllItems($vars) 
{
	$mysql = new MySQLController($_GET['database']);
	$response = new ResponseController();

    $mysql->getBranch(null, true);

    $response->handleMySQLResult($mysql);
    $response->send();
}

function getOneItem($vars)
{
    //var_dump($vars, $_GET['database']); die;

    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if ($mysql->hasErrors()) {
        $response->handleMySQLResult($mysql);
        $response->send();
        return null;
    }

    $isCategory = false;

    // Якщо це категорія i вона не остання:
    if ($mysql->result[0]['type'] == 'category') {
        if(is_array($mysql->result[0]['tags'])) {
            if (!in_array('last', $mysql->result[0]['tags'])) {
                // довантажую до неї лише дочірні категорії:
                $isCategory = true;
            }
        }
    }

    $mysql->getBranch($mysql->result[0]['uuid'], $isCategory);

    $response->handleMySQLResult($mysql);
    $response->send();

}

function breadCrumbs($vars)
{
    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->getBreadCrumbs($vars['uuid']);
    $mysql->result = array_reverse($mysql->result); // тут би хук заюзати!

    $response->handleMySQLResult($mysql);
    $response->send();
}


function putOneItem($vars)
{
    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if ($mysql->hasErrors()) {
        $response->handleMySQLResult($mysql);
        $response->send();
        return null;
    }

    $mysql->updateItem($vars['uuid'], ResponseController::readRequestData());

    $response->handleMySQLResult($mysql);
    $response->send();

}


function postItem($vars)
{

    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->createItem(ResponseController::readRequestData());

    $response->handleMySQLResult($mysql);
    $response->send();

}


function deleteItems($vars)
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->deleteItem($request);

    foreach($mysql->result as $key => $recordToDestroy) {
        //uuid
        $mysql->result[$key] = $recordToDestroy['uuid'];
    }

    $response->handleMySQLResult($mysql);

    $response->send();

}























function postItems($vars) 
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController();
	$response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if (count($mysql->result) == 0) {
        $response->httpStatus(404);
        $response->responseData([]);
        $response->send();
        http_response_code(404);
        die();
    }

    $response->sqlQueries($mysql->sql);
    $response->responseData($mysql->result);

	$response->send();
}

function putItems($vars)
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController();
	$response = new ResponseController();

	$response->responseData(['putItems' => $vars, 'raw' => $request]);
	$response->send();
}


function makeBackup($vars)
{
    BackupController::makeBackup($_GET['database']);
}


function searchItems($vars)
{
    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $mysql->searchItems(ResponseController::readRequestData());

    $response->handleMySQLResult($mysql);
    $response->send();
}

function uploadFiles($vars)
{
    $mysql = new MySQLController($_GET['database']);
    $response = new ResponseController();

    $storageDir = 'storage/'; // todo path uuid

    foreach($_FILES as $file) {
        $uploadfile = $storageDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
            // echo "File is valid, and was successfully uploaded.\n";
            $response->responseData(['f' => 'File is valid, and was successfully uploaded. ' . $uploadfile]);
        } else {
            // echo "Possible file upload attack!\n";
            $response->responseData(['f' => 'Possible file upload attack! ' . $uploadfile]);
        }
    }



   // $response->responseData(['vars' => $vars, '$_FILES' => $_FILES]);
    $response->send();
}