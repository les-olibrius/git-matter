<?php

namespace LesOlibrius\GitMatter;

use Silex\Application;

class Page
{

    /**
     * @var Application
     */
    private $app;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $mode;

    /**
     * Page constructor.
     * @param Application $app
     * @param string $path
     * @param string $mode
     */
    public function __construct(Application $app, $path, $mode)
    {
        $this->app = $app;
        $this->path = $path;
        $this->mode = $mode;
        $this->parsedContent = null;
    }

    /**
     * @return string HTML file
     */
    public function render()
    {
        $content = file_get_contents($this->path);
        $datas = $this->parseContent($content);

        if (count($datas) <= 1) {
            $this->app->abort(404, 'missing_content');
        }

        return $this->app['twig']->render($this->mode.'.html.twig', $datas);
    }

    /**
     * @param string $content
     * @return array
     */
    private function parseContent($content)
    {
        $parser = $this->app['frontmatter'];

        $document = $parser->parse($content);

        $metas = $document->getYAML();
        $metas['page_layout_datas'] = $document->getContent();

        return $metas;
    }
}