<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class ParameterizedTriggersPublisher implements PublisherInterface
{
    private $triggers = [];

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    public function addTrigger(ParameterizedTriggerPublisher $trigger): self
    {
        $this->triggers[] = $trigger;

        return $this;
    }
}
