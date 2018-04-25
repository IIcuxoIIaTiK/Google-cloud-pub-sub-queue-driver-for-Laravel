<?php

namespace IIcuxoIIaTiK\GCloudPubSub;

use Illuminate\Queue\Jobs\Job;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Google\Cloud\PubSub\PubSubClient;

class GCloudPubSubJob extends Job implements JobContract
{

    protected $pubSub;
    protected $job;
    protected $subscription;

    public function __construct(
        Container $container,
        PubSubClient $pubSub,
        $queue,
        $subscription,
        $job
    ) {
        $this->pubSub = $pubSub;
        $this->job    = $job;
        $this->queue  = $queue;
        $this->container    = $container;
        $this->subscription = $this->pubSub->subscription($subscription, $this->queue);;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
//    public function fire()
//    {
//        $this->fire(json_decode($this->getRawBody(), true));
//    }

    public function attempts()
    {
        // TODO: Implement attempts() method.
    }

    public function getRawBody()
    {
        return base64_decode($this->job->data());
    }

    public function delete()
    {
        parent::delete();

        $this->subscription->acknowledge($this->job);
    }

    public function release($delay = 0)
    {
        parent::release($delay);

        $this->subscription->modifyAckDeadline($this->job, $delay);
    }

    public function getJobId()
    {
        $MessageId = null;

        return $MessageId;
    }
}