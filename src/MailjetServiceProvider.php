<?php

namespace Mailjet\LaravelMailjet;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Mailjet\LaravelMailjet\Services\MailjetService;
use Mailjet\LaravelMailjet\Transport\MailjetTransport;

class MailjetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Facade
        $this->app->singleton('Mailjet', function ($app) {
            $config = $this->app['config']->get('mail.mailjet', array());
            $call = $this->app['config']->get('mail.mailjet.common.call', true);
            $options = $this->app['config']->get('mail.mailjet.common.options', array());

            return new MailjetService($config['key'], $config['secret'], $call, $options);
        });

        //Mail Driver
        $this->app->afterResolving(MailManager::class, function (MailManager $mailManager) {
            $mailManager->extend("mailjet", function ($config) {
                $config = $this->app['config']->get('mail.mailers.mailjet', array());
                $call = $this->app['config']->get('mail.mailers.mailjet.transactional.call', true);
                $options = $this->app['config']->get('mail.mailers.mailjet.transactional.options', array());

                return new MailjetTransport(new \Swift_Events_SimpleEventDispatcher(), $config['key'], $config['secret'], $call, $options);
            });

        });
    }


    public function provides()
    {
        return ['mailjet'];
    }
}
