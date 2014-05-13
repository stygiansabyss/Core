<?php namespace NukaCode\Core\ServiceProviders;

use Illuminate\Html\HtmlServiceProvider as BaseHtmlServiceProvider;

class HtmlServiceProvider extends BaseHtmlServiceProvider {

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function($app)
        {
            return new \NukaCode\Core\Html\HtmlBuilder($app['url']);
        });
    }

}