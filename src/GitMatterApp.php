<?php

namespace LesOlibrius\GitMatter;

use Silex\Application;

/**
 * Class App
 * @author Maxime Baumann <maxime.baumann.pro@gmail.com>
 * @package LesOlibrius\GitMatter
 */
class GitMatterApp extends \Silex\Application
{
    use Application\TwigTrait;
    use Application\SecurityTrait;
    use Application\TranslationTrait;

}