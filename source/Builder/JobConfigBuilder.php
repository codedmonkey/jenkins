<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder;

use CodedMonkey\Jenkins\Exception\BuilderException;

class JobConfigBuilder
{
    const TYPE_FREESTYLE = 'freestyle';
    const TYPE_FOLDER = 'folder';

    private $type;

    private $displayName;
    private $description;
    private $disabled = false;
    private $triggers = [];
    private $builders = [];

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

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function addTimedTrigger(string $cron): self
    {
        $this->triggers[] = ['timed', $cron];

        return $this;
    }

    public function addShellBuilder(string $command): self
    {
        $this->builders[] = ['shell', $command];

        return $this;
    }

    public function buildConfig()
    {
        $dom = new \DOMDocument('1.1', 'UTF-8');

        $dom->formatOutput = true;

        $rootNode = $this->buildRootNode($dom);
        $dom->appendChild($rootNode);

        if (null !== $this->displayName) {
            $displayNameNode = $dom->createElement('displayName', $this->displayName);
            $rootNode->appendChild($displayNameNode);
        }

        if (null !== $this->description) {
            $descriptionNode = $dom->createElement('description', $this->description);
            $rootNode->appendChild($descriptionNode);
        }

        // todo does not appear in folders
        $disabledNode = $dom->createElement('disabled', $this->disabled ? 'true' : 'false');
        $rootNode->appendChild($disabledNode);

        if (count($this->triggers)) {
            $triggersNode = $dom->createElement('triggers');
            $rootNode->appendChild($triggersNode);

            foreach ($this->triggers as $trigger) {
                $triggerNode = $this->buildTriggerNode($trigger, $dom);

                $triggersNode->appendChild($triggerNode);
            }
        }

        if (count($this->builders)) {
            $buildersNode = $dom->createElement('builders');
            $rootNode->appendChild($buildersNode);

            foreach ($this->builders as $builder) {
                $builderNode = $this->buildBuilderNode($builder, $dom);

                $buildersNode->appendChild($builderNode);
            }
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

    private function buildTriggerNode($trigger, \DOMDocument $dom)
    {
        switch ($trigger[0]) {
            case 'timed':
                $node = $dom->createElement('hudson.triggers.TimerTrigger');

                $specificationNode = $dom->createElement('spec', $trigger[1]);
                $node->appendChild($specificationNode);

                return $node;

            default:
                throw new BuilderException(sprintf('Invalid trigger type: %s', $trigger[0]));
        }
    }

    private function buildBuilderNode($builder, \DOMDocument $dom)
    {
        switch ($builder[0]) {
            case 'shell':
                $node = $dom->createElement('hudson.tasks.Shell');

                $commandNode = $dom->createElement('command', $builder[1]);
                $node->appendChild($commandNode);

                return $node;

            default:
                throw new BuilderException(sprintf('Invalid builder type: %s', $builder[0]));
        }
    }
}
