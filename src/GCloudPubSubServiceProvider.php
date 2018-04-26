<?php

namespace IIcuxoIIaTiK\GCloudPubSub;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class GCloudPubSubServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/gcloud_pubsub.php', 'queue.connections.gcloud_pubsub');

        $this->app['config']->set('queue.connections.gcloud_pubsub.queue_names',
            Config::get('gcloud_pubsub.queue_names', [])
        );
        $this->app['config']->set('queue.connections.gcloud_pubsub.default',
            Config::get('gcloud_pubsub.default', $this->app['config']->get('queue.connections.gcloud_pubsub', []))
        );
    }

    /**
     * Register the application's event listeners.
     * Publish config file
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
                __DIR__ . '/../config/gcloud_pubsub.php' => config_path('gcloud_pubsub.php'),
            ]
        );


        app('queue')->addConnector('gcloud_pubsub',
            function () {
                return new GCloudPubSubConnector();
            }
        );

    }
}


