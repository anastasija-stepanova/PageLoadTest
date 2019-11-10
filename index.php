<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ .'/src/autoloader.inc.php';

const SUCCESSFUL_STATUS = 200;
$siteUrl = 'https://medium.com';

$client = new WebPageTestClient();
$testId = $client->runNewTest($siteUrl);
print_r($testId);

$testStatus = $client->checkStateTest($testId);
if ($testStatus != null && array_key_exists('statusCode', $testStatus) && $testStatus['statusCode'] == SUCCESSFUL_STATUS)
{
    $result = $client->getResult($testId);
    print_r($result);
}
else
{
    print_r('Результаты теста еще не готовы');
}