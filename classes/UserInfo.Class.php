<?php

/**
 * Description of UserInfo
 * 2015-08-15
 * Xmind 中和 User 資訊相關
 *
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 */

namespace ninthday\xmind;

class UserInfo
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

    public function getUserIDByGaccount($gaccount)
    {
        $rtn = 0;
        $sql = 'SELECT `userID` FROM `info_user` WHERE `gaccount` = :gaccount';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':gaccount', $gaccount, \PDO::PARAM_STR);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_NUM);
            $rtn = $rs[0];
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
        }
        return $rtn;
    }
    
    
    public function getUserHardwareID($user_id, $hardware_id, $model, $android_ver, $upload_time){
        $uh_id = $this->checkUserHardware($user_id, $hardware_id, $android_ver);
        if($uh_id == 0){
            $uh_id = $this->addUserHardware($user_id, $hardware_id, $model, $android_ver, $upload_time);
        }
        return $uh_id;
    }
    
    private function checkUserHardware($user_id, $hardware_id, $android_ver){
        $rtn = 0;
        $sql = 'SELECT `UHID` FROM `user_hardware` WHERE `userID`= :userID AND `HardwareID` = :HardwareID AND `AndroidVersion` = :AndroidVersion';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':HardwareID', $hardware_id, \PDO::PARAM_INT);
            $stmt->bindParam(':AndroidVersion', $android_ver, \PDO::PARAM_STR);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_NUM);
            $rtn = $rs[0];
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
        }
        return $rtn;
    }
    
    private function addUserHardware($user_id, $hardware_id, $model, $android_ver, $upload_time){
        $sql = 'INSERT INTO `user_hardware`(`userID`, `HardwareID`, `Model`, `AndroidVersion`, `insertTime`) '
                . 'VALUES (:userID , :HardwareID, :Model, :AndroidVersion, :insertTime)';
        try {
            
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':HardwareID', $hardware_id, \PDO::PARAM_INT);
            $stmt->bindParam(':Model', $model, \PDO::PARAM_STR);
            $stmt->bindParam(':AndroidVersion', $android_ver, \PDO::PARAM_STR);
            $stmt->bindParam(':insertTime', $upload_time, \PDO::PARAM_STR);
            $stmt->execute();
            $newID = $this->dbh->lastInsertId();
            return $newID;
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
        }
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
