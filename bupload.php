<?php

require 'inc/setup.inc.php';
require './vendor/chrisboulton/php-resque/lib/Resque.php';

// include classes
require_once _APP_PATH . 'classes/myPDOConn.Class.php';
require_once _APP_PATH . 'classes/UserInfo.Class.php';
require_once _APP_PATH . 'classes/UploadLog.Class.php';

// Return Message
$msg = '';

try {
    $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('BackendConnConfig.inc.php');
    $objUserInfo = new \ninthday\xmind\UserInfo($pdoConn);
    $objUpl = new \ninthday\xmind\UploadLog($pdoConn);

    $upload_json = file_get_contents("php://input");
    $upload_data = json_decode($upload_json);

    $file = 'test_data/test.json';
    file_put_contents($file, $upload_json);

//$filename = 'test_data/test_2015081102.json';
//$json = file_get_contents($filename, "r");
//$upload_data = json_decode($json);

    $receive_count = count($upload_data->ProbeArray);

    //取得使用者在系統中的編號
    $user_id = $objUserInfo->getUserIDByGaccount($upload_data->UserID);
    if ($user_id == 0) {
        throw new Exception('User Account is Not in Database!');
    }

    $upload_time = date("Y-m-d H:i:s", intval($upload_data->Timestamp / 1000));
    $uh_id = $objUserInfo->getUserHardwareID(
            $user_id, $upload_data->HardwareID, $upload_data->Model,
            $upload_data->AndroidVersion, $upload_data->ApplicationVersion,
            $upload_time);

    //php-resque
    Resque::setBackend('127.0.0.1:6379');

    $probe_job = array();
    foreach ($upload_data->ProbeArray as $probe) {
        switch ($probe->ProbeType) {
            case 'ServicesProbe':
                $probe_job['ServicesProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "process" => $probe->Process
                );
                break;
            case 'Wifi_Status':
                $probe_job['WifiStatus'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "ns" => $probe->NatworkStatus
                );
                break;
            case 'BatteryProbe':
                $probe_job['BatteryProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "bl" => $probe->BatteryLevel
                );
                break;
            case 'LocationProbe':
                $probe_job['LocationProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "lat" => $probe->Latitude,
                    "lng" => $probe->Longitude
                );
                break;
            case 'BluetoothProbe':
                $probe_job['BluetoothProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "rssi" => $probe->RSSI
                );
                break;
            case 'ScreenProbe':
                $probe_job['ScreenProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "son" => $probe->ScreenOn
                );
                break;
            case 'Take_a_New_Photo_Event':
                $probe_job['TakePhotoProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp
                );
                break;
            case 'CallLogProbe':
                $probe_job['CallLogProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "cdt" => $probe->Date,
                    "cdu" => $probe->Duration
                );
                break;
            case 'Current_ForeGround_AppName':
                $probe_job['FGAppNameProbe'][] = array(
                    "user_id" => $user_id,
                    "uh_id" => $uh_id,
                    "trig_time" => $probe->TriggeredTimestamp,
                    "pgn" => $probe->PackageName
                );
                break;
//            case 'Current_Foreground_Camera_AppName':
//                $probe_job['FGCamAppNameProbe'][] = array(
//                    "user_id" => $user_id,
//                    "uh_id" => $uh_id,
//                    "trig_time" => $probe->TriggeredTimestamp,
//                    "pgn" => $probe->PackageName
//                );
//                break;
//            case 'Current_Foreground_Screen_Unlock_AppName':
//                $probe_job['FGScUAppNameProbe'][] = array(
//                    "user_id" => $user_id,
//                    "uh_id" => $uh_id,
//                    "trig_time" => $probe->TriggeredTimestamp,
//                    "pgn" => $probe->PackageName
//                );
//                break;

            default:
                break;
        }
    }

    $result_resque = true;
    $receive_probe = array();
    foreach ($probe_job as $probe_type => $job_args) {
        $result = Resque::enqueue('xmind-userlog', $probe_type, $job_args, true);
        $receive_probe[$probe_type] = count($job_args);
        if (!$result) {
            $result_resque = false;
            $msg.= $probe_type . '-> error.';
        }
    }
    $result_savelog = $objUpl->saveUploadLog($user_id, $uh_id, $upload_time,
            $receive_count, json_encode($receive_probe)
    );

    $result_upload = $result_resque AND $result_savelog;
} catch (Exception $exc) {
    $result_upload = false;
    $receive_count = 0;
    $msg .= $exc->getMessage();
} catch (PDOException $pexc) {
    $result_upload = false;
    $receive_count = 0;
    $msg .= $exc->getMessage();
}

//歸還連線資源
unset($pdoConn);

$result = array(
    "state" => $result_upload,
    "count" => $receive_count,
    "msg" => $msg
);

$up_rslog = '[' . date("Y-m-d H:i:s") . '] UserID-' . $user_id . ' (HID-' . $uh_id . ') result: ';
$up_rslog .= print_r($result, true) . PHP_EOL;
file_put_contents(_APP_PATH . "upload-result-log/" . date("Y-m-d") . "-uplog.txt",
        $up_rslog, FILE_APPEND | LOCK_EX);

header("Content-type: application/json; charset=utf-8");

echo json_encode($result);
