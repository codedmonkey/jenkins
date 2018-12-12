<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class TriggerPublisher implements PublisherInterface
{
    private $job;
    private $threshold = 'success';

    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * Sets the job to trigger
     */
    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getThreshold(): string
    {
        return $this->threshold;
    }

    /**
     * Sets the threshold for when to trigger the job: success, unstable
     * or failure (defaults to success)
     */
    public function setThreshold(string $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }
}
