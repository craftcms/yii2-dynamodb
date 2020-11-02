<?php

use yii\web\Application;

error_reporting(-1);

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require_once(dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php');
require_once(dirname(__DIR__) . '/vendor/autoload.php');

$config = require(__DIR__ . '/config/app.php');

try {
    $app = new Application($config);
} catch (Exception $e) {
    die($e->getMessage());
}
