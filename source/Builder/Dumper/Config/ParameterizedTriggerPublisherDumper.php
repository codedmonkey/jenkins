<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

use CodedMonkey\Jenkins\Builder\Config\ParameterizedTriggerPublisher;
use CodedMonkey\Jenkins\Builder\Config\ParameterizedTriggersPublisher;
use CodedMonkey\Jenkins\Exception\RuntimeException;

class ParameterizedTriggerPublisherDumper
{
    public function dump(\DOMDocument $dom, $publisher)
    {
        if (!$publisher instanceof ParameterizedTriggerPublisher && !$publisher instanceof ParameterizedTriggersPublisher) {
            throw new RuntimeException(sprintf('Invalid publisher class: %s', get_class($publisher)));
        }

        $node = $dom->createElement('hudson.plugins.parameterizedtrigger.BuildTrigger');
        $node->setAttribute('plugin', 'parameterized-trigger');

        $configsNode = $dom->createElement('configs');
        $node->appendChild($configsNode);

        $triggers = $publisher instanceof ParameterizedTriggerPublisher ? [$publisher] : $publisher->getTriggers();

        foreach ($triggers as $trigger) {
            $triggerNode = $this->dumpTrigger($dom, $trigger);
            $configsNode->appendChild($triggerNode);
        }

        return $node;
    }

    public function dumpTrigger(\DOMDocument $dom, ParameterizedTriggerPublisher $trigger): \DOMElement
    {
        $node = $dom->createElement('hudson.plugins.parameterizedtrigger.BuildTriggerConfig');

        $configsNode = $dom->createElement('configs');
        $node->appendChild($configsNode);

        if (count($trigger->getPredefinedParameters())) {
            $predefinedParameters = implode(PHP_EOL, array_map(function($key, $value) {
                return sprintf('%s=%s', $key, $value);
            }, array_keys($trigger->getPredefinedParameters()), $trigger->getPredefinedParameters()));

            $predefinedParametersNode = $dom->createElement('hudson.plugins.parameterizedtrigger.PredefinedBuildParameters');
            $configsNode->appendChild($predefinedParametersNode);

            $predefinedParametersPropertiesNode = $dom->createElement('properties', $predefinedParameters);
            $predefinedParametersNode->appendChild($predefinedParametersPropertiesNode);

            // todo make configurable
            $textParametersNode = $dom->createElement('textParamValueOnNewLine', 'false');
            $predefinedParametersNode->appendChild($textParametersNode);
        }

        if ($trigger->getCurrentParameters()) {
            $currentParametersNode = $dom->createElement('hudson.plugins.parameterizedtrigger.CurrentBuildParameters');
            $configsNode->appendChild($currentParametersNode);
        }

        if ($trigger->getCurrentNode()) {
            $currentNodeNode = $dom->createElement('hudson.plugins.parameterizedtrigger.NodeParameters');
            $configsNode->appendChild($currentNodeNode);
        }

        if (!$configsNode->hasChildNodes()) {
            $configsNode->setAttribute('class', 'empty-list');
        }

        $projectsNode = $dom->createElement('projects', implode(' ,', $trigger->getJobs()));
        $node->appendChild($projectsNode);

        $conditionNode = $dom->createElement('condition', strtoupper($trigger->getCondition()));
        $node->appendChild($conditionNode);

        $noParametersNode = $dom->createElement('triggerWithNoParameters', $trigger->getTriggerWithNoParameters() ? 'true' : 'false');
        $node->appendChild($noParametersNode);

        $fromChildProjectsNode = $dom->createElement('triggerFromChildProjects', $trigger->getTriggerFromChildProjects() ? 'true' : 'false');
        $node->appendChild($fromChildProjectsNode);

        return $node;
    }
}
