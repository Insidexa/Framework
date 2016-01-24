<?php

require_once(__DIR__.'/../framework/Loader.php');

Loader::addNamespacePath('Framework\\',__DIR__.'/../framework');
Loader::addNamespacePath('Blog\\',__DIR__.'/../src/Blog');

Loader::register();

$app = new Application(__DIR__.'/../app/config/config.php');

$app->run();