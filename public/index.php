<?php

namespace LesOlibrius\GitMatter;

require '../vendor/autoload.php';

// CONST

define('DS', DIRECTORY_SEPARATOR);
define('CONTENT_PATH', dirname(__DIR__) . DS . 'content');
define('CONFIG_PATH', dirname(__DIR__) . DS . 'config');

$app = new GitMatterApp();

$app->run();
