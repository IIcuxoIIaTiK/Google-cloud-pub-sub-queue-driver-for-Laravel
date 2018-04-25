<?php

return [
    'driver' => 'gcloud_pubsub',

    'default' => [
        'projectId'    => getenv('GC_PROJECT_ID'),
        'topic'        => getenv('GC_PUBSUB_TOPIC'),
        'subscription' => getenv('GC_PUBSUB_SUBSCRIPTION'),
        'keyFilePath'  => getenv('GC_AUTH_JSON'),
        'ttl'          => (getenv('GC_PUBSUB_TTL')) ? getenv('GC_PUBSUB_TTL') : 100,
    ],

    'queue_names' => [
//        'example' => [
//            Queue connection settings, key = name queue worker, just here you can override the default settings for gcpubsub queues.
//            If the value is missing then the default value is used
//            'projectId' => 1,
//            'topic' => 'example',
//            'subscription' => 'example',
//            'keyFilePath' => 'path/to/your/key',
//            'ttl' => 100,
//        ],
    ],

];