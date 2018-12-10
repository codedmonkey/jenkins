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
    private $publishers = [];

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

    /**
     * Cleans up the job's workspace through the Workspace Cleanup Plugin
     *
     * @param array       $includePatterns  List of files to be removed
     * @param array       $excludePatterns  List of files to keep intact
     * @param array       $cleanWhen        Clean the directory depending on the build state: success, unstable, failure, notBuilt or aborted (defaults to true)
     * @param bool        $matchDirectories Apply patterns on directories (defaults to false)
     * @param bool        $failBuild        Fail build when cleanup fails (defaults to false)
     * @param bool        $cleanupParent    Cleanup matrix parent workspace (defaults to false)
     * @param string|null $externalCommand  External command to cleanup workspace
     * @param bool        $deferredWipeout  Use deferred wipeout (defaults to true)
     *
     * @return JobConfigBuilder
     */
    public function addWorkspaceCleanupPublisher(array $includePatterns = [], array $excludePatterns = [], array $cleanWhen = [], bool $matchDirectories = false, bool $failBuild = false, bool $cleanupParent = false, ?string $externalCommand = null, bool $deferredWipeout = true): self
    {
        $this->publishers[] = [
            'workspace-cleanup',
            $cleanWhen,
            $failBuild,
            [
                $includePatterns,
                $excludePatterns,
            ],
            $matchDirectories,
            $cleanupParent,
            $deferredWipeout,
            $externalCommand,
        ];

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

        $dumper->buildActionsNode();
        $dumper->buildDescriptionNode($this->description);
        $dumper->buildDisplayNameNode($this->displayName);
        $dumper->buildKeepDependenciesNode();
        $dumper->buildParametersNode($this->parameters);
        $dumper->buildSourceControlManagementNode();
        $dumper->buildCanRoamNode();
        $dumper->buildDisabledNode($this->disabled);
        $dumper->buildBlockBuildWhenDownstreamBuildingNode();
        $dumper->buildBlockBuildWhenUpstreamBuildingNode();
        $dumper->buildTriggersNode($this->triggers);
        $dumper->buildConcurrentBuildNode();
        $dumper->buildBuildersNode($this->builders);
        $dumper->buildPublishersNode($this->publishers);
        $dumper->buildWrappersNode();

        return $dumper->dump();
    }
}
