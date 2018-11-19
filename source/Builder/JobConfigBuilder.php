<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder;

class JobConfigBuilder
{
    private $description;

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function buildConfig()
    {
        $dom = new \DOMDocument('1.1', 'UTF-8');

        $dom->formatOutput = true;

        $projectNode = $dom->createElement('project');
        $dom->appendChild($projectNode);

        if (null !== $this->description) {
            $descriptionNode = $dom->createElement('description', $this->description);
            $projectNode->appendChild($descriptionNode);
        }

        return $dom->saveXML();
    }
}
