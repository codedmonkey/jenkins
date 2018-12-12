<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

use CodedMonkey\Jenkins\Builder\Config\TimedTrigger;

class TimedTriggerDumper
{
    public function dump(\DOMDocument $dom, TimedTrigger $trigger)
    {
        $node = $dom->createElement('hudson.triggers.TimerTrigger');

        $specificationNode = $dom->createElement('spec', $trigger->getCron());
        $node->appendChild($specificationNode);

        return $node;
    }
}
