<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ($argc < 1) {
    die; // only run from command line
}

require 'inc/setup.inc.php';

// include classes
require_once _APP_PATH . 'classes/myPDOConn.Class.php';
require_once _APP_PATH . 'classes/DataCleaner.Class.php';

$result = '';

try {
    $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('BackendConnConfig.inc.php');
    $objCleaner = new \ninthday\xmind\DataCleaner($pdoConn);
    
    $arrMsgs = $objCleaner->clearAllDuplicate();
    
    foreach ($arrMsgs as $message) {
        $result .= $message . PHP_EOL;
    }
    
} catch (Exception $exc) {
    $result .= $exc->getMessage();
}

//歸還連線資源
unset($pdoConn);

//將執行結果寫入檔案
file_put_contents(_APP_PATH . "dailyCleaner-log/" . date("Y-m-d") . "-log.txt",
        $result, FILE_APPEND | LOCK_EX);