<?php

namespace IIcuxoIIaTiK\GCloudPubSub;

use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class GCloudPubSubQueue extends Queue implements QueueContract
{

    /**
     * The Google PubSub Instance
     *
     * @var \Google\Cloud\PubSub\PubSubClient;
     */
    protected $pubSub;

    /**
     * The Google PubSub Topic
     *
     * @var string;
     */

    private $config;
    private $projectId;
    private $topic;
    private $subscription;
    private $keyFilePath;
    private $ttl;

    /**
     * GCloudPubSubQueue constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getConfigForQueue($queue = null)
    {
        $result_config = $this->config['default'];

        if ($queue != null && array_key_exists($queue, $this->config['queue_names'])) {
            $config = $this->config['queue_names'][$queue];

            if (array_key_exists('projectId', $config)) {
                $result_config['projectId'] = $config['projectId'];
            }
            if (array_key_exists('topic', $config)) {
                $result_config['topic'] = $config['topic'];
            }
            if (array_key_exists('subscription', $config)) {
                $result_config['subscription'] = $config['subscription'];
            }
            if (array_key_exists('keyFilePath', $config)) {
                $result_config['keyFilePath'] = $config['keyFilePath'];
            }
            if (array_key_exists('ttl', $config)) {
                $result_config['ttl'] = $config['ttl'];
            }
        }
        $this->projectId    = $result_config['projectId'];
        $this->topic        = $result_config['topic'];
        $this->subscription = $result_config['subscription'];
        $this->keyFilePath  = $result_config['keyFilePath'];
        $this->ttl          = $result_config['ttl'];

        return $result_config;
    }

    public function setPubSub($queue = null)
    {
        $config_pubsub = $this->getConfigForQueue($queue);
        $this->pubSub  = new PubSubClient($config_pubsub);
    }

    public function size($queue = null)
    {
        $this->setPubSub($queue);
        $subscription = $this->getSubscription();

        return count(iterator_to_array($subscription->pull()));
    }

    private function getSubscription()
    {
        return $this->pubSub->subscription($this->subscription, $this->topic);
    }

    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {

        $this->setPubSub($queue);

        $topic    = $this->pubSub->topic($this->topic);
        $response = $topic->publish([
                'data'       => base64_encode($payload),
                'attributes' => null
            ]
        );

        return $response['messageIds'][0];
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     *
     * @return string
     */
    protected function createPayload($job, $data = '', $queue = null)
    {
        $payload             = parent::createPayload($job, $data);
        $payload             = json_decode($payload, true);
        $payload['attempts'] = 1;

        return json_encode($payload);
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.

    }

    public function pop($queue = null)
    {
        $this->setPubSub($queue);
        $subscription = $this->getSubscription();

        $pullOptions = [
            'returnImmediately' => true,
            'maxMessages'       => 1
        ];

        $messages = $subscription->pull($pullOptions);

        if (count($messages) > 0) {
            $subscription->modifyAckDeadline($messages[0], $this->ttl);

            return new GCloudPubSubJob($this->container, $this->pubSub, $this->topic, $this->subscription, $messages[0]
            );
        }
    }
}
