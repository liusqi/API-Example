<?php
require_once 'api/myapi.php';

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new MyAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
} 
catch (Exception $e) {
    echo json_encode(array('Error' => true, 'ErrorMessage' => $e->getMessage()));
}