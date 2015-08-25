<?php

class BluetoothProbe
{

    public function perform()
    {
        require_once _APP_PATH . 'classes/myPDOConn.Class.php';
        $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('BackendConnConfig.inc.php');

        $sql = 'INSERT INTO `probe_bluetooth_log`(`userID`, `UHID`, `TriggeredTimes`, `RSSI`) '
                . 'VALUES (:userID, :UHID, :TriggeredTimes, :RSSI)';
        try {
            $stmt = $pdoConn->dbh->prepare($sql);
            if ($stmt) {
                $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
                $stmt->bindParam(':UHID', $uh_id, \PDO::PARAM_INT);
                $stmt->bindParam(':TriggeredTimes', $trig_time, \PDO::PARAM_STR);
                $stmt->bindParam(':RSSI', $rssi, \PDO::PARAM_INT);

                foreach ($this->args as $probe_log) {
                    $user_id = $probe_log["user_id"];
                    $uh_id = $probe_log["uh_id"];
                    $trig_time = date("Y-m-d H:i:s", intval($probe_log["trig_time"] / 1000));
                    $rssi = $probe_log["rssi"];
                    $stmt->execute();
                }
            } else {
                $err_msg = json_encode('[' . date("Y-m-d H:i:s") . '] BluetoothProbe Job Error:' .
                        $pdoConn->dbh->errorInfo() . PHP_EOL . json_encode($this->args));
                file_put_contents(_APP_PATH . "xmind-job/error-log/" . date("Y-m-d") . "-error.txt", $err_msg, FILE_APPEND | LOCK_EX);
            }
        } catch (Exception $exc) {
            $err_msg = '[' . date("Y-m-d H:i:s") . '] BluetoothProbe Job Exception:' .
                    $exc->getMessage() . PHP_EOL . json_encode($this->args);
            $err_msg .= PHP_EOL . '-----------------------------------------------' . PHP_EOL;
            file_put_contents(_APP_PATH . "xmind-job/error-log/" . date("Y-m-d") . "-exception.txt", $err_msg, FILE_APPEND | LOCK_EX);
            unset($pdoConn);
        }
        unset($pdoConn);
    }

}
