<?php

namespace LesOlibrius\GitMatter;

use Silex\Application;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Class GitMatterApp
 * @author Maxime Baumann <maxime.baumann.pro@gmail.com>
 * @package LesOlibrius\GitMatter
 */
class GitMatterApp extends \Silex\Application
{
    use Application\TwigTrait;
    use Application\SecurityTrait;
    use Application\TranslationTrait;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var string
     */
    private $configDirPath;

    /**
     * @var string
     */
    private $localesDirPath;

    /**
     * @var string
     */
    private $viewsDirPath;

    /**
     * @var string
     */
    private $contentDirPath;

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->setPaths();

        $this->registerProviders();
        $this->setRoutes();
    }

    private function setPaths()
    {
        $this->rootPath = dirname(__DIR__);
        $this->configDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'config';
        $this->localesDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'locales';
        $this->viewsDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'views';
        $this->contentDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'content';
    }

    private function registerProviders()
    {
        $self = $this;

        // Session
        $this->register(new \Silex\Provider\SessionServiceProvider());

        // Locale and Translation
        #region Locale and translattion
        $this->register(new \Silex\Provider\LocaleServiceProvider());
        $this->register(
            new \Silex\Provider\TranslationServiceProvider(),
            array(
                'locale_fallbacks' => array('en'),
            )
        );

        $this->extend(
            'translator',
            function ($translator, $self) use ($self) {
                $translator->addLoader('yaml', new YamlFileLoader());

                $localesPathFormat = $self->localesDirPath.DIRECTORY_SEPARATOR.'*.yml';
                foreach (glob($localesPathFormat) as $file) {
                    $locale = str_replace([$self->localesDirPath.DIRECTORY_SEPARATOR, '.yml'], '', $file);

                    $translator->addResource('yaml', $file, $locale);
                }

                return $translator;
            }
        );
        #endregion

        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->register(
            new \Silex\Provider\TwigServiceProvider(),
            array(
                'twig.path' => $this->viewsDirPath,
            )
        );
    }

    private function setRoutes()
    {
        $self = $this;
        $this->get(
            '/',
            function () use ($self) {
                $url = '/'.$self['locale'];

                return $this->redirect($url);
            }
        );

        $this->get(
            '/{_locale}/{wikiPage}',
            function ($wikiPage) use ($self) {
                return $wikiPage;
            }
        )
            ->value('wikiPage', 'index');
    }

}