<?php

use Symfony\Component\Dotenv\Dotenv;

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'production');

require dirname(dirname(__DIR__)) . '/application/vendor/autoload.php';
require dirname(dirname(__DIR__)) . '/application/vendor/yiisoft/yii2/Yii.php';

$dotenv = new Dotenv();
$dotenv->load( dirname(dirname(__DIR__)) . '/application/.env');

$applicationConfig = require dirname(dirname(__DIR__)) . '/application/config/web.php';
$prodConfig = require dirname(__DIR__) . '/config/web.php';
$config = array_merge_recursive($applicationConfig, $prodConfig);

(new yii\web\Application($config))->run();
