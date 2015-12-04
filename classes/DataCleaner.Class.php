<?php

/**
 * Description of DataCleaner
 * 2015-12-03
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 */

namespace ninthday\xmind;

class DataCleaner
{

    private $dbh = null;

    /**
     * 建構子包含連線設定
     * @param \ninthday\niceToolbar\myPDOConn $pdoConn myPDOConn object
     */
    function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->dbh = $pdoConn->dbh;
    }
    
    /**
     * 清除各個資料表的重複資料
     * 
     * @return array 清除結果的訊息陣列
     */
    public function clearAllDuplicate()
    {
        $resultMsgs = array();
        array_push($resultMsgs, $this->clearBatteryLog());
        array_push($resultMsgs, $this->clearBluetoothLog());
        array_push($resultMsgs, $this->clearCallLog());
        array_push($resultMsgs, $this->clearFppLog());
        array_push($resultMsgs, $this->clearLocationLog());
        array_push($resultMsgs, $this->clearPhotoLog());
        array_push($resultMsgs, $this->clearScreenLog());
        array_push($resultMsgs, $this->clearServiceLog());
        array_push($resultMsgs, $this->clearWifiLog());
        return $resultMsgs;
    }

    /**
     * 刪除重複的電池 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearBatteryLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_battery_log` AS `A`
	INNER JOIN `probe_battery_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`BatteryLevel` = `B`.`BatteryLevel`
	AND `A`.`BatteryID` > `B`.`BatteryID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_battery_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearBatteryLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的藍芽 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearBluetoothLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_bluetooth_log` AS `A`
	INNER JOIN `probe_bluetooth_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`BluetoothID` > `B`.`BluetoothID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_bluetooth_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearBluetoothLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的撥打電話 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearCallLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_call_log` AS `A`
	INNER JOIN `probe_call_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`CallDate` = `B`.`CallDate`
	AND `A`.`CallID` > `B`.`CallID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_call_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearCallLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的前景程式 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearFppLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_fgapp_log` AS `A`
	INNER JOIN `probe_fgapp_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`PackageName` = `B`.`PackageName`
	AND `A`.`FGAppID` > `B`.`FGAppID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_fgapp_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearFppLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的位置 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearLocationLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_location_log` AS `A`
	INNER JOIN `probe_location_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`LocationID` < `B`.`LocationID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_location_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearLocationLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的照相 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearPhotoLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_photo_log` AS `A`
	INNER JOIN `probe_photo_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`TPhotoID` > `B`.`TPhotoID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_photo_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearPhotoLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的螢幕開關 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearScreenLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_screen_log` AS `A`
	INNER JOIN `probe_screen_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`ScreenOn` = `B`.`ScreenOn`
	AND `A`.`ScreenID` > `B`.`ScreenID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_screen_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearScreenLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的背景服務程式 log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearServiceLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_service_log` AS `A`
	INNER JOIN `probe_service_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`Process` = `B`.`Process`
	AND `A`.`ServiceID` > `B`.`ServiceID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_service_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearServiceLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
    }

    /**
     * 刪除重複的 WIFI log 記錄
     * 
     * @return string 刪除的筆數敘述
     * @throws Exception
     */
    private function clearWifiLog()
    {
        $delMessage = '';
        $sql = 'DELETE `A`
	FROM `probe_wifi_log` AS `A`
	INNER JOIN `probe_wifi_log` AS `B`
	ON `A`.`UHID` = `B`.`UHID` 
	AND `A`.`TriggeredTimes` = `B`.`TriggeredTimes`
	AND `A`.`NatworkStatus` = `B`.`NatworkStatus`
	AND `A`.`WifiID` > `B`.`WifiID`';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $count = $stmt->rowCount();
            $delMessage = 'Deleted probe_wifi_log ' . $count . ' rows.';
        } catch (\PDOException $exc) {
            $exptMsg = 'Some Problem in DataCleaner::clearWifiLog Function. Message: ' . $exc->getMessage();
            throw new \Exception($exptMsg);
        }
        return $delMessage;
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
