<?php

namespace IIcuxoIIaTiK\GCloudPubSub;

use Illuminate\Support\ServiceProvider;

class GCloudPubSubServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(config_path('gcloud_pubsub.php'),
            'queue.connections.gcloud_pubsub'
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
        app('queue')->addConnector('gcloud_pubsub',
            function () {
                return new GCloudPubSubConnector();
            }
        );

        $this->publishes([
            __DIR__ . '../../gcloud_pubsub.php' => config_path('gcloud_pubsub.php'),
        ]
        );
    }
}


