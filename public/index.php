<?php

namespace LesOlibrius\GitMatter;

require '../vendor/autoload.php';

$app = new GitMatterApp(['debug' => true]);

$app->run();
