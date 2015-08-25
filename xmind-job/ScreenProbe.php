<?php

class ScreenProbe
{

    public function perform()
    {
        require_once _APP_PATH . 'classes/myPDOConn.Class.php';
        $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('BackendConnConfig.inc.php');

        $sql = 'INSERT INTO `probe_screen_log`(`userID`, `UHID`, `TriggeredTimes`, `ScreenOn`) '
                . 'VALUES (:userID, :UHID, :TriggeredTimes, :ScreenOn)';
        try {
            $stmt = $pdoConn->dbh->prepare($sql);
            if ($stmt) {
                $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
                $stmt->bindParam(':UHID', $uh_id, \PDO::PARAM_INT);
                $stmt->bindParam(':TriggeredTimes', $trig_time, \PDO::PARAM_STR);
                $stmt->bindParam(':ScreenOn', $screenon, \PDO::PARAM_BOOL);

                foreach ($this->args as $probe_log) {
                    $user_id = $probe_log["user_id"];
                    $uh_id = $probe_log["uh_id"];
                    $trig_time = date("Y-m-d H:i:s", intval($probe_log["trig_time"] / 1000));
                    $screenon = ($probe_log["son"] === 'true') ? true : false;
                    $stmt->execute();
                }
            } else {
                $err_msg = json_encode('[' . date("Y-m-d H:i:s") . '] ScreenProbe Job Error:' .
                        $pdoConn->dbh->errorInfo() . PHP_EOL . json_encode($this->args));
                file_put_contents(_APP_PATH . "xmind-job/error-log/" . date("Y-m-d") . "-error.txt", $err_msg, FILE_APPEND | LOCK_EX);
            }
        } catch (Exception $exc) {
            $err_msg = '[' . date("Y-m-d H:i:s") . '] ScreenProbe Job Exception:' .
                    $exc->getMessage() . PHP_EOL . json_encode($this->args);
            $err_msg .= PHP_EOL . '-----------------------------------------------' . PHP_EOL;
            file_put_contents(_APP_PATH . "xmind-job/error-log/" . date("Y-m-d") . "-exception.txt", $err_msg, FILE_APPEND | LOCK_EX);
            unset($pdoConn);
        }
        unset($pdoConn);
    }

}
