<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

use CodedMonkey\Jenkins\Builder\Config\TriggerPublisher;
use CodedMonkey\Jenkins\Exception\BuilderException;

class TriggerPublisherDumper
{
    public function dump(\DOMDocument $dom, TriggerPublisher $publisher)
    {
        $node = $dom->createElement('hudson.tasks.BuildTrigger');

        $projectsNode = $dom->createElement('childProjects', $publisher->getJob());
        $node->appendChild($projectsNode);

        $thresholdValues = $this->getThresholdValues($publisher->getThreshold());

        $thresholdNode = $dom->createElement('threshold');
        $node->appendChild($thresholdNode);

        $thresholdNameNode = $dom->createElement('name', $thresholdValues['name']);
        $thresholdNode->appendChild($thresholdNameNode);

        $thresholdOrdinalNode = $dom->createElement('ordinal', $thresholdValues['ordinal']);
        $thresholdNode->appendChild($thresholdOrdinalNode);

        $thresholdColorNode = $dom->createElement('color', $thresholdValues['color']);
        $thresholdNode->appendChild($thresholdColorNode);

        $completeBuildNode = $dom->createElement('completeBuild', 'true');
        $thresholdNode->appendChild($completeBuildNode);

        return $node;
    }

    private function getThresholdValues(string $threshold): array
    {
        static $values = [
            'success' => [
                'name' => 'SUCCESS',
                'ordinal' => 0,
                'color' => 'BLUE',
            ],
            'unstable' => [
                'name' => 'UNSTABLE',
                'ordinal' => 1,
                'color' => 'YELLOW',
            ],
            'failure' => [
                'name' => 'FAILURE',
                'ordinal' => 2,
                'color' => 'RED',
            ],
        ];

        if (!isset($values[$threshold])) {
            throw new BuilderException(sprintf('Invalid threshold value: %s', $threshold));
        }

        return $values[$threshold];
    }
}
