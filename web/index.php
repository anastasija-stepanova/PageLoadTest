<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/autoloader.inc.php';

$templateLoader = new Twig_Loader_Filesystem('../src/templates/');
$twig = new Twig_Environment($templateLoader);
$template = $twig->load('layout.tpl');
echo $template->render([
    'content' => $twig->load('main_page.tpl'),
]);