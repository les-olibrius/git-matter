<?php

namespace LesOlibrius\GitMatter;


use Pimple\Container;

class Page
{

    /**
     * @var Container;
     */
    private $container;

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
     * @param Container $container
     * @param string $path
     * @param string $mode
     */
    public function __construct(Container $container, $path, $mode)
    {
        $this->container = $container;
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

        return $this->container['twig']->render($this->mode . '.html.twig', $datas);
    }

    /**
     * @param string $content
     * @return array
     */
    private function parseContent($content)
    {
        $parser = $this->container['frontmatter'];

        $document = $parser->parse($content);

        $metas = $document->getYAML();
        $metas['page_layout_datas'] = $document->getContent();

        return $metas;
    }
}