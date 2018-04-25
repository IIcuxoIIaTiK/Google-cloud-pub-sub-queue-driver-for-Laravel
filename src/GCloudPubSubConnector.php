<?php

namespace IIcuxoIIaTiK\GCloudPubSub;

use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Connectors\ConnectorInterface;

class GCloudPubSubConnector implements ConnectorInterface
{

    /**
     * @param array $config
     *
     * @return mixed
     */
    public function connect(array $config)
    {
        return new GCloudPubSubQueue($config);
    }
}
