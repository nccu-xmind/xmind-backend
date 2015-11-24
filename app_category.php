<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require './inc/setup.inc.php';

require_once _APP_PATH . 'classes/myPDOConn.Class.php';
require_once _APP_PATH . 'classes/GooglePlay.Class.php';

$pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('BackendConnConfig.inc.php');

$dbh = $pdoConn->dbh;
$sql = 'SELECT `id`, `pkgName` FROM `info_pkg` WHERE `category` IS NULL';
$sql_upt = 'UPDATE `info_pkg` SET `type`=:type,`category`=:category,`google_category`=:gcategory,`appname`=:appname,`company_name`=:company_name,`company_id`=:company_id WHERE `id`=:id';
try {
    $stmt = $dbh->prepare($sql);
    if($stmt->execute()){
        echo 'Get ' . $stmt->rowCount() . ' NULL rows.' . PHP_EOL;
    }
    $stmt_upt = $dbh->prepare($sql_upt);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        echo PHP_EOL . 'Crawl package name: ' . $row['pkgName'] . PHP_EOL;
        $objGooglePlay = new \ninthday\charlotte\GooglePlay($row['pkgName']);
        $app_profile = $objGooglePlay->getAppInformation();
        $stmt_upt->bindParam(':type', $app_profile['category']['type'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':category', $app_profile['category']['main'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':gcategory', $app_profile['category']['google'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':appname', $app_profile['name'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':company_name', $app_profile['company']['name'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':company_id', $app_profile['company']['id'], PDO::PARAM_STR);
        $stmt_upt->bindParam(':id', $row['id'], PDO::PARAM_STR);
        $stmt_upt->execute();
        if (!is_string($app_profile['company']['id']) && $app_profile['company']['id'] == 0){
            $sleeptime = 0;
        }else{
            $sleeptime = rand(1, 5);
        }
        echo $app_profile['name'] . ' + ' . $app_profile['company']['id'] . ' ---> ';
        echo $sleeptime . PHP_EOL;
        sleep($sleeptime);
        
        unset($app_profile);
        unset($objGooglePlay);
    }
} catch (Exception $exc) {
    echo $exc->getMessage();
}

unset($dbh);
unset($pdoConn);
