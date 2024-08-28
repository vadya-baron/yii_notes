<?php

$host = $_ENV['APP_DB_HOST'] ?? 'localhost';
$dbname = $_ENV['APP_DB_NAME'] ?? 'notes';
$username = $_ENV['APP_DB_USERNAME'] ?? 'notes';
$password = $_ENV['APP_DB_PASSWORD'] ?? 'notes';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host={$host};dbname={$dbname}",
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
