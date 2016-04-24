<?php

require_once __DIR__ . '/../vendor/autoload.php';
/*require_once(__DIR__ . '/../Framework/Loader.php');

Loader::addNamespacePath('Blog\\', __DIR__ . '/../src/Blog');
Loader::addNamespacePath('CMS\\', __DIR__ . '/../src/CMS');
*/
$app = new \Framework\Application(__DIR__ . '/../app/config/config.php');

$app->run();