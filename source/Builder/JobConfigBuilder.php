<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder;

class JobConfigBuilder
{
    const TYPE_FREESTYLE = 'freestyle';
    const TYPE_FOLDER = 'folder';

    private $type;

    private $displayName;
    private $description;

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function buildConfig()
    {
        $dom = new \DOMDocument('1.1', 'UTF-8');

        $dom->formatOutput = true;

        $rootNode = $this->buildRootNode($dom);
        $dom->appendChild($rootNode);

        if (null !== $this->displayName) {
            $descriptionNode = $dom->createElement('displayName', $this->description);
            $rootNode->appendChild($descriptionNode);
        }

        if (null !== $this->description) {
            $descriptionNode = $dom->createElement('description', $this->description);
            $rootNode->appendChild($descriptionNode);
        }

        return $dom->saveXML();
    }

    private function buildRootNode(\DOMDocument $dom)
    {
        switch ($this->type) {
            case self::TYPE_FOLDER:
                $node = $dom->createElement('com.cloudbees.hudson.plugins.folder.Folder');
                $node->setAttribute('plugin', 'cloudbees-folder@6.6');

                return $node;

            case self::TYPE_FREESTYLE:
            default:
                return $dom->createElement('project');
        }
    }
}
