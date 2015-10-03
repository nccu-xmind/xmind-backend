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

    /**
     * 由 Google 帳號取得系統裡使用者的標號
     * 
     * @access public
     * @param string $gaccount Google Account
     * @return int 使用者編號
     * @since version 1.0
     */
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
            $exptMsg = 'Some Problem in UserInfo->getUserIDByGaccount Function. Message: ' . $exc->getMessage();
            throw new Exception($exptMsg);
        }
        return $rtn;
    }

    /**
     * 取得使用者的硬體配對編號
     * 
     * @access public
     * @param int $user_id 使用者系統編號
     * @param int $hardware_id 行動裝置硬體編號
     * @param string $model 行動裝置的模組名稱
     * @param string $androidVersion 行動裝置的Android版本
     * @param string $xmindAppVersion 行動裝置安裝的軟體版本
     * @param string $upload_time 上傳時間
     * @return int 硬體配對編號
     * @since version 1.0
     */
    public function getUserHardwareID($user_id, $hardware_id, $model,
            $androidVersion, $xmindAppVersion, $upload_time)
    {
        $uh_id = $this->checkUserHardware($user_id, $hardware_id,
                $androidVersion, $xmindAppVersion);
        if ($uh_id == 0) {
            $uh_id = $this->addUserHardware($user_id, $hardware_id, $model,
                    $androidVersion, $xmindAppVersion, $upload_time);
        }
        return $uh_id;
    }

    /**
     * 檢查使用者的硬體配對是不是存在，返回硬體配對編號或零
     * 
     * @access private
     * @param int $user_id 使用者系統編號
     * @param int $hardware_id 行動裝置硬體編號
     * @param string $androidVersion 行動裝置的Android版本
     * @param string $xmindAppVersion 行動裝置安裝的軟體版本
     * @return int 硬體配對編號或零
     * @throws Exception
     * @since version 1.0
     */
    private function checkUserHardware($user_id, $hardware_id, $androidVersion,
            $xmindAppVersion)
    {
        $rtn = 0;
        $sql = 'SELECT `UHID` FROM `user_hardware` '
                . 'WHERE `userID`= :userID AND `HardwareID` = :HardwareID '
                . 'AND `AndroidVersion` = :AndroidVersion AND `AppVersion` = :AppVersion';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':HardwareID', $hardware_id, \PDO::PARAM_INT);
            $stmt->bindParam(':AndroidVersion', $androidVersion, \PDO::PARAM_STR);
            $stmt->bindParam(':AppVersion', $xmindAppVersion, \PDO::PARAM_STR);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_NUM);
            $rtn = $rs[0];
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in UserInfo->checkUserHardware Function. Message: ' . $exc->getMessage();
            throw new Exception($exptMsg);
        }
        return $rtn;
    }

    /**
     * 新增使用者的硬體配對
     * 
     * @access private
     * @param int $user_id 使用者系統編號
     * @param int $hardware_id 行動裝置硬體編號
     * @param string $model 行動裝置的模組名稱
     * @param string $androidVersion 行動裝置的Android版本
     * @param string $xmindAppVersion 行動裝置安裝的軟體版本
     * @param string $upload_time 上傳時間
     * @return int 硬體配對編號
     * @throws Exception
     * @since version 1.0
     */
    private function addUserHardware($user_id, $hardware_id, $model,
            $androidVersion, $xmindAppVersion, $upload_time)
    {
        $sql = 'INSERT INTO `user_hardware`(`userID`, `HardwareID`, `Model`, `AndroidVersion`, `AppVersion`, `insertTime`) '
                . 'VALUES (:userID , :HardwareID, :Model, :AndroidVersion, :AppVersion, :insertTime)';
        try {

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':userID', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':HardwareID', $hardware_id, \PDO::PARAM_INT);
            $stmt->bindParam(':Model', $model, \PDO::PARAM_STR);
            $stmt->bindParam(':AndroidVersion', $androidVersion, \PDO::PARAM_STR);
            $stmt->bindParam(':AppVersion', $xmindAppVersion, \PDO::PARAM_STR);
            $stmt->bindParam(':insertTime', $upload_time, \PDO::PARAM_STR);
            $stmt->execute();
            $newID = $this->dbh->lastInsertId();
            return $newID;
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in UserInfo->AddUserHardware Function. Message: ' . $exc->getMessage();
            throw new Exception($exptMsg);
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
