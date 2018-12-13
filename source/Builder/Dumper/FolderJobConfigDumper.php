<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper;

use CodedMonkey\Jenkins\Builder\Config\BuilderInterface;
use CodedMonkey\Jenkins\Builder\Config\ParameterInterface;
use CodedMonkey\Jenkins\Builder\Config\PublisherInterface;
use CodedMonkey\Jenkins\Builder\Config\TriggerInterface;

class FolderJobConfigDumper extends AbstractJobConfigDumper
{
    public function buildRootNode(\DOMDocument $dom): void
    {
        $this->rootNode = $dom->createElement('com.cloudbees.hudson.plugins.folder.Folder');
        $this->rootNode->setAttribute('plugin', 'cloudbees-folder');
        $this->dom->appendChild($this->rootNode);
    }

    public function buildKeepDependenciesNode(): void
    {

    }

    public function buildParametersNode(array $parameters): void
    {

    }

    public function buildParameterNode(\DOMElement $parent, ParameterInterface $parameter): void
    {

    }

    public function buildCanRoamNode(): void
    {

    }

    public function buildDisabledNode(bool $disabled): void
    {

    }

    public function buildBlockDownstreamNode(): void
    {

    }

    public function buildBlockUpstreamNode(): void
    {

    }

    public function buildTriggersNode(array $triggers): void
    {

    }

    public function buildTriggerNode(\DOMElement $parent, TriggerInterface $trigger): void
    {

    }

    public function buildConcurrentNode(): void
    {

    }

    public function buildBuildersNode(array $builders): void
    {

    }

    public function buildBuilderNode(\DOMElement $parent, BuilderInterface $builder): void
    {

    }

    public function buildPublishersNode(array $publishers): void
    {

    }

    public function buildPublisherNode(\DOMElement $parent, PublisherInterface $publisher): void
    {

    }

    public function buildWrappersNode(): void
    {

    }

    public function buildFolderViewsNode(array $views): void
    {
        // todo
        if (0 === count($views)) {
            return;
        }

        $node = $this->dom->createElement('folderViews');
        $node->setAttribute('class', 'com.cloudbees.hudson.plugins.folder.views.DefaultFolderViewHolder');
        $this->rootNode->appendChild($node);

        $viewsNode = $this->dom->createElement('views');
        $node->appendChild($viewsNode);

        $this->buildFolderViewNode($viewsNode, 'hudson.model.AllView');

        $tabsNode = $this->dom->createElement('tabBar');
        $tabsNode->setAttribute('class', 'hudson.views.DefaultViewsTabBar');
        $viewsNode->appendChild($tabsNode);
    }

    public function buildFolderViewNode(\DOMElement $parent, string $viewType): void
    {
        $node = $this->dom->createElement($viewType);
        $parent->appendChild($node);

        $ownerNode = $this->dom->createElement('owner');
        $ownerNode->setAttribute('class', 'com.cloudbees.hudson.plugins.folder.Folder');
        $ownerNode->setAttribute('reference', '../../../..');
        $node->appendChild($ownerNode);

        $nameNode = $this->dom->createElement('name', 'All');
        $node->appendChild($nameNode);

        $filterExecutorsNode = $this->dom->createElement('filterExecutors', 'false');
        $node->appendChild($filterExecutorsNode);

        $filterQueueNode = $this->dom->createElement('filterQueue', 'false');
        $node->appendChild($filterQueueNode);

        $propertiesNode = $this->dom->createElement('properties');
        $propertiesNode->setAttribute('class', 'hudson.model.View$PropertyList');
        $node->appendChild($propertiesNode);
    }

    public function buildHealthMetricsNode(): void
    {
        // todo
        $node = $this->dom->createElement('healthMetrics');
        $this->rootNode->appendChild($node);
    }

    public function buildIconNode(): void
    {
        // todo
        $node = $this->dom->createElement('icon');
        $node->setAttribute('class', 'com.cloudbees.hudson.plugins.folder.icons.StockFolderIcon');
        $this->rootNode->appendChild($node);
    }
}
