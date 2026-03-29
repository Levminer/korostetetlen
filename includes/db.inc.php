<?php

function getDbConnection()
{
    $configFile = __DIR__ . '/db.config.php';

    if (!file_exists($configFile)) {
        throw new RuntimeException('A DB konfiguracio hianyzik. Masold le az includes/db.config.php.example fajlt includes/db.config.php nevvel.');
    }

    $dbConfig = include $configFile;

    $dsn = 'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['dbname'];
    $dbh = new PDO(
        $dsn,
        $dbConfig['user'],
        $dbConfig['password'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if (isset($dbConfig['charset_sql']) && $dbConfig['charset_sql'] !== '') {
        $dbh->query($dbConfig['charset_sql']);
    }

    return $dbh;
}
