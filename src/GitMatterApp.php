<?php

namespace LesOlibrius\GitMatter;

use LesOlibrius\GitMatter\Providers\PageServiceProvider;
use Mni\FrontYAML\Parser;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * GitMatterApp constructor. Instanciate a new App.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->setPaths();

        $this->registerProviders();
        $this->setServices();
        $this->setRoutes();
    }

    /**
     * Set the paths of the app.
     */
    private function setPaths()
    {
        $this->rootPath = dirname(__DIR__);
        $this->configDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'config';
        $this->localesDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'locales';
        $this->viewsDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'views';
        $this->contentDirPath = $this->rootPath.DIRECTORY_SEPARATOR.'content';
    }

    /**
     * Register the App providers
     */
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

        $this->register(
            new PageServiceProvider(),
            [
                'page.default_mode' => 'view',
            ]
        );
    }

    /**
     * Set application Dependencies/Services
     */
    private function setServices()
    {
        $this['frontmatter'] = function ($c) {
            return new Parser();
        };
    }

    /**
     * Set the routes of the app
     */
    private function setRoutes()
    {
        $self = $this;

        // Redirect to the current locale index
        $this->get(
            '/',
            function () use ($self) {
                $url = '/'.$self['locale'];

                return $this->redirect($url);
            }
        );

        // Localized wiki pages
        $this->get(
            '/{_locale}/{wikiPage}',
            function (GitMatterApp $self, Request $request, $wikiPage) {
                $filepath = $this->contentDirPath.DIRECTORY_SEPARATOR.$self['locale'].DIRECTORY_SEPARATOR.$wikiPage.'.yaml';

                return $self['page']($filepath, $request->get('mode'));
            }
        )
            ->value('wikiPage', 'index');
    }

}