<?php

namespace LesOlibrius\GitMatter;

require '../vendor/autoload.php';

// CONST

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__));

define('CONTENT_PATH', ROOT_PATH . DS . 'content');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('LOCALES_PATH', ROOT_PATH . DS . 'locales');
define('VIEWS_PATH', ROOT_PATH . DS . 'views');

$app = new GitMatterApp();

# region Providers

$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => VIEWS_PATH,
));

# endregion Providers

$app->get('/{_locale}/{wikiPage}', function ($wikiPage) use ($app){

});

$app->run();
