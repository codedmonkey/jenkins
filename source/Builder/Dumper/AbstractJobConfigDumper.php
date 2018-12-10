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

    public function buildBlockBuildWhenDownstreamBuildingNode(): void
    {
        // todo
        $node = $this->dom->createElement('blockBuildWhenDownstreamBuilding', 'false');
        $this->rootNode->appendChild($node);
    }

    public function buildBlockBuildWhenUpstreamBuildingNode(): void
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

    public function buildConcurrentBuildNode(): void
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

    public function buildPublishersNode(array $publishers): void
    {
        $node = $this->dom->createElement('publishers');
        $this->rootNode->appendChild($node);

        foreach ($publishers as $publisher) {
            $this->buildPublisherNode($node, $publisher);
        }
    }

    public function buildPublisherNode(\DOMElement $parent, $publisher): void
    {
        switch ($publisher[0]) {
            case 'workspace-cleanup':
                $node = $this->dom->createElement('hudson.plugins.ws__cleanup.WsCleanup');
                $node->setAttribute('plugin', 'ws-cleanup');
                $parent->appendChild($node);

                $patterns = array_merge(
                    array_map(function($pattern) {
                        return ['include', $pattern];
                    }, $publisher[3][0]),
                    array_map(function($pattern) {
                        return ['exclude', $pattern];
                    }, $publisher[3][1])
                );

                if (count($patterns) > 0) {
                    $patternsNode = $this->dom->createElement('patterns');
                    $node->appendChild($patternsNode);

                    foreach ($patterns as $pattern) {
                        $patternNode = $this->dom->createElement('hudson.plugins.ws__cleanup.Pattern');
                        $patternsNode->appendChild($patternNode);

                        $patternTextNode = $this->dom->createElement('pattern', $pattern[0]);
                        $patternNode->appendChild($patternTextNode);

                        $patternTypeNode = $this->dom->createElement('type', strtoupper($pattern[1]));
                        $patternNode->appendChild($patternTypeNode);
                    }
                }
                else {
                    $patternsNode = $this->dom->createElement('patterns');
                    $patternsNode->setAttribute('class', 'empty-list');
                    $node->appendChild($patternsNode);
                }

                $deleteDirectoriesNode = $this->dom->createElement('deleteDirs', $publisher[4] ? 'true' : 'false');
                $node->appendChild($deleteDirectoriesNode);

                // todo make configurable
                $skipNode = $this->dom->createElement('skipWhenFailed', 'false');
                $node->appendChild($skipNode);

                static $buildStates = ['success', 'unstable', 'failure', 'notBuilt', 'aborted'];

                foreach ($buildStates as $buildState) {
                    $clean = $publisher[1][$buildState] ?? true;

                    $cleanNode = $this->dom->createElement(sprintf('cleanWhen%s', ucfirst($buildState)), $clean ? 'true' : 'false');
                    $node->appendChild($cleanNode);
                }

                $failNode = $this->dom->createElement('notFailBuild', $publisher[2] ? 'false' : 'true');
                $node->appendChild($failNode);

                $cleanupParentNode = $this->dom->createElement('cleanupMatrixParent', $publisher[5] ? 'true' : 'false');
                $node->appendChild($cleanupParentNode);

                $externalCommandNode = $this->dom->createElement('externalDelete', $publisher[7]);
                $node->appendChild($externalCommandNode);

                $deferredWipeoutNode = $this->dom->createElement('notFailBuild', $publisher[6] ? 'false' : 'true');
                $node->appendChild($deferredWipeoutNode);

                break;

            default:
                throw new BuilderException(sprintf('Invalid publisher type: %s', $publisher[0]));
        }
    }

    public function buildWrappersNode(): void
    {
        // todo
        $node = $this->dom->createElement('buildWrappers');
        $this->rootNode->appendChild($node);
    }
}
