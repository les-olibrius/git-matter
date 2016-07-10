<?php

namespace LesOlibrius\GitMatter\Providers;

use LesOlibrius\GitMatter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PageServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['page'] = $app->protect(
            function ($path, $mode) use ($app) {
                $defaultmode = $app['page.default_mode'] ? $app['page.default_mode'] : '';
                $mode = $mode ?: $defaultmode;

                $page = new GitMatter\Page($app, $path, $mode);

                return $page->render();
            }
        );
    }


}