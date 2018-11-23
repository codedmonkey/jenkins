<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder;

use CodedMonkey\Jenkins\Builder\Dumper\AbstractJobConfigDumper;
use CodedMonkey\Jenkins\Builder\Dumper\FolderJobConfigDumper;
use CodedMonkey\Jenkins\Builder\Dumper\FreestyleJobConfigDumper;
use CodedMonkey\Jenkins\Exception\BuilderException;

class JobConfigBuilder
{
    const TYPE_FREESTYLE = 'freestyle';
    const TYPE_FOLDER = 'folder';

    private $type;

    private $displayName;
    private $description;
    private $disabled = false;
    private $parameters = [];
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

    public function addParameter(string $name, string $type = 'string', ?string $defaultValue = null, ?string $description = null): self
    {
        $this->parameters[] = [$type, $name, $description, $defaultValue];

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
        static $typeMap = [
            self::TYPE_FREESTYLE => FreestyleJobConfigDumper::class,
            self::TYPE_FOLDER => FolderJobConfigDumper::class,
        ];

        if (!isset($typeMap[$this->type])) {
            throw new BuilderException('Invalid job type');
        }

        /** @var AbstractJobConfigDumper $dumper */
        $dumper = new $typeMap[$this->type];

        $dumper->buildDisplayNameNode($this->displayName);
        $dumper->buildDescriptionNode($this->description);
        $dumper->buildDisabledNode($this->disabled);
        $dumper->buildParametersNode($this->parameters);
        $dumper->buildTriggersNode($this->triggers);
        $dumper->buildBuildersNode($this->builders);

        return $dumper->dump();
    }
}
