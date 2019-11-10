<?php

require_once '../autoloader.inc.php';

function connectDB()
{
    $errorMessage = 'Невозможно подключиться к серверу базы данных';
    $conn = new mysqli(Config::MYSQL_HOST, Config::MYSQL_USERNAME, Config::MYSQL_PASSWORD, Config::MYSQL_DATABASE);
    if (!$conn)
    {
        echo $errorMessage;
    }
    else
    {
        $query = $conn->query('set names utf8');
        if (!$query)
            echo $errorMessage;
        else
            return $conn;
    }
}

function getMigrationFiles($conn) {
    $sqlFolder = str_replace('\\', '/', realpath(dirname(__FILE__)) . '/sql/');
    $allFiles = glob($sqlFolder . '*.sql');

    $query = sprintf('show tables from `%s` like "%s"', Config::MYSQL_DATABASE, Config::DB_TABLE_VERSIONS);
    $data = $conn->query($query);
    $firstMigration = !$data->num_rows;

    if ($firstMigration)
    {
        return $allFiles;
    }

    $versionsFiles = [];

    $query = sprintf('select `name` from `%s`', Config::DB_TABLE_VERSIONS);
    $data = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

    foreach ($data as $row) {
        array_push($versionsFiles, $sqlFolder . $row['name']);
    }

    return array_diff($allFiles, $versionsFiles);
}

function migrate($conn, $file)
{
    $baseName = basename($file);

    $sql = file_get_contents(__DIR__ . '/sql/' . $baseName);
    $query = sprintf('insert into `%s` (`name`) values("%s")', Config::DB_TABLE_VERSIONS, $baseName);
    $sql = $sql . $query;
    $conn->multi_query($sql);
}