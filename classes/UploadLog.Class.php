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

    /**
     * 儲存使用者的上傳記錄
     * 
     * @access public
     * @param int $user_id 使用者系統編號
     * @param int $uh_id 硬體配對編號
     * @param string $upl_time 上傳時間
     * @param int $receive_count 收到數量
     * @param type $receive_probe
     * @return boolean 成功或失敗
     * @since version 1.0
     */
    public function saveUploadLog($user_id, $uh_id, $upl_time, $receive_count, $receive_probe)
    {
        $rtn = false;
        $user_ip = $this->getClientIP();
        $save_time = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO `upload_log` (`userID`, `UHID`, `uploadTime`, `saveTime`, `receiveCount`, `ClientIP`, `receiveProbe`) '
                . 'VALUES (:userID, :UHID, :uploadTime, :saveTime, :receiveCount, :ClientIP, :receiveProbe)';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':UHID', $uh_id, \PDO::PARAM_INT);
            $stmt->bindParam(':uploadTime', $upl_time, \PDO::PARAM_STR);
            $stmt->bindParam(':saveTime', $save_time, \PDO::PARAM_STR);
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

    /**
     * 取得使用者的 IP 位址
     * 
     * @access private
     * @return string IP Adddress
     * @since version 1.0
     */
    private function getClientIP()
    {
        $myip = '';
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
