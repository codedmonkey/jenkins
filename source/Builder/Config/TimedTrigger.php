<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class TimedTrigger implements TriggerInterface
{
    private $cron;

    public function getCron(): string
    {
        return $this->cron;
    }

    /**
     * Sets when to run the job (in cron syntax)
     */
    public function setCron(string $cron): self
    {
        $this->cron = $cron;

        return $this;
    }
}
