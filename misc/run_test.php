<?php
require_once __DIR__ . '/../src/autoloader.inc.php';

if ($argc != 2)
{
    echo 'Неверно передан параметр!';
    exit();
}

$siteUrl = $argv[1];
$database = new Database(Config::MYSQL_HOST, Config::MYSQL_DATABASE, Config::MYSQL_USERNAME, Config::MYSQL_PASSWORD);
$apiKey = $database->executeQuery("SELECT api_key FROM " . DatabaseTable::USER .
    " WHERE id = ?", [Config::DEFAULT_USER_ID], PDO::FETCH_COLUMN);
$client = new WebPageTestClient($apiKey);
$wptTestId = $client->runNewTest($siteUrl);
$database->executeQuery("INSERT INTO " . DatabaseTable::TEST_INFO . " (user_id, test_id) 
                         VALUES (?, ?)", [Config::DEFAULT_USER_ID, $wptTestId]);
$dataArray = $database->selectOneRow("SELECT id FROM " . DatabaseTable::TEST_INFO .
    " WHERE test_id = ?", [$wptTestId]);
echo (array_key_exists('id', $dataArray)) ? $dataArray['id'] : '';