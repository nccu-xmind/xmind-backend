<?php
require '../inc/setup.inc.php';

// Worker 執行的 Queue 任務名稱
putenv("QUEUE=xmind-userlog");

// Worker 的數量
putenv("COUNT=1");

// Worker 檢查 Queue 的時間間隔
putenv("INTERVAL=30");

// 要不要記錄
putenv("VERBOSE=1");    //簡易的 Log 輸出在螢幕上
//putenv("VVERBOSE=1");   //詳細的 Log 輸出在螢幕上，Debug 時候使用
//putenv("LOGGING=1");

require _APP_PATH . 'vendor/chrisboulton/php-resque/demo/bad_job.php';
require _APP_PATH . 'vendor/chrisboulton/php-resque/demo/php_error_job.php';

// include All Job Class
require _APP_PATH . 'xmind-job/ServicesProbe.php';
require _APP_PATH . 'xmind-job/WifiStatus.php';
require _APP_PATH . 'xmind-job/BatteryProbe.php';
require _APP_PATH . 'xmind-job/LocationProbe.php';
require _APP_PATH . 'xmind-job/BluetoothProbe.php';
require _APP_PATH . 'xmind-job/ScreenProbe.php';
require _APP_PATH . 'xmind-job/TakePhotoProbe.php';
require _APP_PATH . 'xmind-job/CallLogProbe.php';
require _APP_PATH . 'xmind-job/FGAppNameProbe.php';
require _APP_PATH . 'xmind-job/FGCamAppNameProbe.php';
require _APP_PATH . 'xmind-job/FGScUAppNameProbe.php';

require _APP_PATH . 'vendor/chrisboulton/php-resque/resque.php';
