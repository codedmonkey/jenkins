<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper;

use CodedMonkey\Jenkins\Builder\Config\BuilderInterface;
use CodedMonkey\Jenkins\Builder\Config\PublisherInterface;
use CodedMonkey\Jenkins\Builder\Config\ShellBuilder;
use CodedMonkey\Jenkins\Builder\Config\TimedTrigger;
use CodedMonkey\Jenkins\Builder\Config\TriggerInterface;
use CodedMonkey\Jenkins\Builder\Config\WorkspaceCleanupPublisher;
use CodedMonkey\Jenkins\Builder\Dumper\Config\ShellBuilderDumper;
use CodedMonkey\Jenkins\Builder\Dumper\Config\TimedTriggerDumper;
use CodedMonkey\Jenkins\Builder\Dumper\Config\WorkspaceCleanupPublisherDumper;
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

    public function buildActionsNode(): void
    {
        // todo
        $node = $this->dom->createElement('actions');
        $this->rootNode->appendChild($node);
    }

    public function buildDescriptionNode(?string $description): void
    {
        $node = $this->dom->createElement('description', $description);
        $this->rootNode->appendChild($node);
    }

    public function buildDisplayNameNode(?string $displayName): void
    {
        if (!$displayName) {
            return;
        }

        $node = $this->dom->createElement('displayName', $displayName);
        $this->rootNode->appendChild($node);
    }

    public function buildKeepDependenciesNode(): void
    {
        // todo
        $node = $this->dom->createElement('keepDependencies', 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildParametersNode(array $parameters): void
    {
        if (0 === count($parameters)) {
            return;
        }

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
            'string' => 'hudson.model.StringParameterDefinition',
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

    public function buildSourceControlManagementNode(): void
    {
        // todo
        $node = $this->dom->createElement('scm');
        $node->setAttribute('class', 'hudson.scm.NullSCM');
        $this->rootNode->appendChild($node);
    }

    public function buildCanRoamNode(): void
    {
        // todo
        $node = $this->dom->createElement('canRoam', 'true');
        $this->rootNode->appendChild($node);
    }

    public function buildDisabledNode(bool $disabled): void
    {
        $node = $this->dom->createElement('disabled', $disabled ? 'true' : 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildBlockDownstreamNode(): void
    {
        // todo
        $node = $this->dom->createElement('blockBuildWhenDownstreamBuilding', 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildBlockUpstreamNode(): void
    {
        // todo
        $node = $this->dom->createElement('blockBuildWhenUpstreamBuilding', 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildTriggersNode(array $triggers): void
    {
        $node = $this->dom->createElement('triggers');
        $this->rootNode->appendChild($node);

        foreach ($triggers as $trigger) {
            $this->buildTriggerNode($node, $trigger);
        }
    }

    public function buildTriggerNode(\DOMElement $parent, TriggerInterface $trigger): void
    {
        static $dumperClasses = [
            TimedTrigger::class => TimedTriggerDumper::class,
        ];

        $class = get_class($trigger);

        if (!isset($dumperClasses[$class])) {
            throw new BuilderException(sprintf('Invalid trigger type: %s', $class));
        }

        $dumper = new $dumperClasses[$class];

        $node = $dumper->dump($this->dom, $trigger);
        $parent->appendChild($node);
    }

    public function buildConcurrentNode(): void
    {
        // todo
        $node = $this->dom->createElement('concurrentBuild', 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildBuildersNode(array $builders): void
    {
        $node = $this->dom->createElement('builders');
        $this->rootNode->appendChild($node);

        foreach ($builders as $builder) {
            $this->buildBuilderNode($node, $builder);
        }
    }

    public function buildBuilderNode(\DOMElement $parent, BuilderInterface $builder): void
    {
        static $dumperClasses = [
            ShellBuilder::class => ShellBuilderDumper::class,
        ];

        $class = get_class($builder);

        if (!isset($dumperClasses[$class])) {
            throw new BuilderException(sprintf('Invalid builder type: %s', $class));
        }

        $dumper = new $dumperClasses[$class];

        $node = $dumper->dump($this->dom, $builder);
        $parent->appendChild($node);
    }

    public function buildPublishersNode(array $publishers): void
    {
        $node = $this->dom->createElement('publishers');
        $this->rootNode->appendChild($node);

        foreach ($publishers as $publisher) {
            $this->buildPublisherNode($node, $publisher);
        }
    }

    public function buildPublisherNode(\DOMElement $parent, PublisherInterface $publisher): void
    {
        static $dumperClasses = [
            WorkspaceCleanupPublisher::class => WorkspaceCleanupPublisherDumper::class,
        ];

        $class = get_class($publisher);

        if (!isset($dumperClasses[$class])) {
            throw new BuilderException(sprintf('Invalid publisher type: %s', $class));
        }

        $dumper = new $dumperClasses[$class];

        $node = $dumper->dump($this->dom, $publisher);
        $parent->appendChild($node);
    }

    public function buildWrappersNode(): void
    {
        // todo
        $node = $this->dom->createElement('buildWrappers');
        $this->rootNode->appendChild($node);
    }

    public function buildFolderViewsNode(array $views): void
    {

    }

    public function buildFolderViewNode(\DOMElement $parent, string $viewType): void
    {

    }

    public function buildHealthMetricsNode(): void
    {

    }

    public function buildIconNode(): void
    {

    }
}
