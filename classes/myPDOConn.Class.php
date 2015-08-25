<?php

/**
 * Description of myPDOConn
 * Singleton 設計的 PDO 連結資料庫程式
 *
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2014, Jeffy Shih
 */

namespace ninthday\niceToolbar;

class myPDOConn
{

    protected static $instance = NULL;
    // Handle of the database connexion
    public $dbh;

    final private function __construct($fileConfig)
    {
        try {
            include _APP_PATH . 'inc/' . $fileConfig;
            $dsn = $pdoConfig['DB_DRIVER'] . ':host=' . $pdoConfig['DB_HOST'] .
                    ';dbname=' . $pdoConfig['DB_NAME'] .
                    ';port=' . $pdoConfig['DB_PORT'] .
                    ';connect_timeout=30';
            $this->dbh = new \PDO($dsn, $pdoConfig['DB_USER'], $pdoConfig['DB_PASSWD'], $pdoConfig['DB_OPTIONS']);
            $this->dbh->exec("SET NAMES 'utf8'");
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * To avoid copies
     */
    final private function __clone()
    {
        
    }

    public static function getInstance($fileConfig)
    {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object($fileConfig);
        }
        return self::$instance;
    }

    final public function __destruct()
    {
        $this->dbh = NULL;
        self::$instance = NULL;
    }

}

?>
