<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper;

use CodedMonkey\Jenkins\Exception\BuilderException;

abstract class AbstractJobConfigDumper
{
    protected $dom;
    /** @var \DOMElement */
    protected $rootNode;

    public function __construct()
    {
        $this->dom = new \DOMDocument('1.1', 'UTF-8');

        $this->dom->formatOutput = true;

        $this->buildRootNode($this->dom);
    }

    public function dump(): string
    {
        return $this->dom->saveXML();
    }

    public function buildRootNode(\DOMDocument $dom): void
    {
        $this->rootNode = $dom->createElement('project');
        $this->dom->appendChild($this->rootNode);
    }

    public function buildDisplayNameNode(?string $displayName): void
    {
        $node = $this->dom->createElement('displayName', $displayName);
        $this->rootNode->appendChild($node);
    }

    public function buildDescriptionNode(?string $description): void
    {
        $node = $this->dom->createElement('description', $description);
        $this->rootNode->appendChild($node);
    }

    public function buildDisabledNode(bool $disabled): void
    {
        $node = $this->dom->createElement('disabled', $disabled ? 'true' : 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildParametersNode(array $parameters): void
    {
        $propertiesNode = $this->dom->createElement('properties');
        $this->rootNode->appendChild($propertiesNode);

        $outerDefinitionsNode = $this->dom->createElement('hudson.model.ParametersDefinitionProperty');
        $propertiesNode->appendChild($outerDefinitionsNode);

        $definitionsNode = $this->dom->createElement('parameterDefinitions');
        $outerDefinitionsNode->appendChild($definitionsNode);

        foreach ($parameters as $parameter) {
            $this->buildParameterNode($definitionsNode, $parameter);
        }
    }

    public function buildParameterNode(\DOMElement $parent, array $parameter): void
    {
        static $typeMap = [
            'password' => 'hudson.model.PasswordParameterDefinition',
        ];

        if (!isset($typeMap[$parameter[0]])) {
            throw new BuilderException(sprintf('Invalid parameter type: %s', $parameter[0]));
        }

        $node = $this->dom->createElement($typeMap[$parameter[0]]);
        $parent->appendChild($node);

        $node->appendChild($this->dom->createElement('name', $parameter[1]));

        if ($parameter[2]) {
            $node->appendChild($this->dom->createElement('description', $parameter[2]));
        }

        if ($parameter[3]) {
            $node->appendChild($this->dom->createElement('defaultValue', $parameter[3]));
        }
    }

    public function buildTriggersNode(array $triggers): void
    {
        $node = $this->dom->createElement('triggers');
        $this->rootNode->appendChild($node);

        foreach ($triggers as $trigger) {
            $this->buildTriggerNode($node, $trigger);
        }
    }

    public function buildTriggerNode(\DOMElement $parent, $trigger): void
    {
        switch ($trigger[0]) {
            case 'timed':
                $node = $this->dom->createElement('hudson.triggers.TimerTrigger');
                $parent->appendChild($node);

                $specificationNode = $this->dom->createElement('spec', $trigger[1]);
                $node->appendChild($specificationNode);

                break;

            default:
                throw new BuilderException(sprintf('Invalid trigger type: %s', $trigger[0]));
        }
    }

    public function buildBuildersNode(array $builders): void
    {
        $node = $this->dom->createElement('builders');
        $this->rootNode->appendChild($node);

        foreach ($builders as $builder) {
            $this->buildBuilderNode($node, $builder);
        }
    }

    public function buildBuilderNode(\DOMElement $parent, $builder): void
    {
        switch ($builder[0]) {
            case 'shell':
                $node = $this->dom->createElement('hudson.tasks.Shell');
                $parent->appendChild($node);

                $commandNode = $this->dom->createElement('command', $builder[1]);
                $node->appendChild($commandNode);

                break;

            default:
                throw new BuilderException(sprintf('Invalid builder type: %s', $builder[0]));
        }
    }
}
