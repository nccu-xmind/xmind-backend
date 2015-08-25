<?php

/**
 * Description of UploadLog
 * 2015-08-15
 * 使用者上傳的 log 記錄
 *
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 */

namespace ninthday\xmind;

class UploadLog
{

    private $dbh = null;

    /**
     * 建構子包含連線設定
     * @param \ninthday\niceToolbar\myPDOConn $pdoConn myPDOConn object
     */
    public function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->dbh = $pdoConn->dbh;
    }

    public function saveUploadLog($user_id, $uh_id, $upl_time, $receive_count, $receive_probe)
    {
        $rtn = false;
        $user_ip = $this->getClientIP();
        $sql = 'INSERT INTO `upload_log` (`userID`, `UHID`, `uploadTime`, `receiveCount`, `ClientIP`, `receiveProbe`) '
                . 'VALUES (:userID, :UHID, :uploadTime, :receiveCount, :ClientIP, :receiveProbe)';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':UHID', $uh_id, \PDO::PARAM_INT);
            $stmt->bindParam(':uploadTime', $upl_time, \PDO::PARAM_STR);
            $stmt->bindParam(':receiveCount', $receive_count, \PDO::PARAM_INT);
            $stmt->bindParam(':ClientIP', $user_ip, \PDO::PARAM_STR);
            $stmt->bindParam(':receiveProbe', $receive_probe, \PDO::PARAM_STR);
            $rs = $stmt->execute();
            if ($rs > 0) {
                $rtn = true;
            }
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
        }
        return $rtn;
    }

    private function getClientIP()
    {
        $myip= '';
        if (!empty(filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP'))) {
            $myip = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } else if (!empty(filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'))) {
            $myip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else {
            $myip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }
        return $myip;
    }

    /**
     * 解構子歸還資源
     */
    public function __destruct()
    {
        $this->dbh = null;
        unset($this->dbh);
    }

}
