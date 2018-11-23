<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper;

class FolderJobConfigDumper extends AbstractJobConfigDumper
{
    public function buildRootNode(\DOMDocument $dom): void
    {
        $this->rootNode = $dom->createElement('com.cloudbees.hudson.plugins.folder.Folder');
        $this->rootNode->setAttribute('plugin', 'cloudbees-folder');
        $this->dom->appendChild($this->rootNode);
    }

    public function buildDisabledNode(bool $disabled): void
    {

    }

    public function buildParametersNode(array $parameters): void
    {

    }

    public function buildParameterNode(\DOMElement $parent, array $parameter): void
    {

    }

    public function buildTriggersNode(array $triggers): void
    {

    }

    public function buildTriggerNode(\DOMElement $parent, $trigger): void
    {

    }

    public function buildBuildersNode(array $builders): void
    {

    }

    public function buildBuilderNode(\DOMElement $parent, $builder): void
    {

    }
}
