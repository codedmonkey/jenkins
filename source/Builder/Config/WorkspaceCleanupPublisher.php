<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class WorkspaceCleanupPublisher implements PublisherInterface
{
    private $includePatterns = [];
    private $excludePatterns = [];
    private $cleanWhen = [];
    private $matchDirectories = false;
    private $failBuild = false;
    private $cleanupParent = false;
    private $externalCommand;
    private $deferredWipeout = true;

    public function getIncludePatterns(): array
    {
        return $this->includePatterns;
    }

    /**
     * Sets the list of files to be removed
     *
     * @param array|string $patterns
     */
    public function setIncludePatterns($patterns): self
    {
        $this->includePatterns = (array) $patterns;

        return $this;
    }

    public function getExcludePatterns(): array
    {
        return $this->excludePatterns;
    }

    /**
     * Sets the list of files to keep intact
     *
     * @param array|string $patterns
     */
    public function setExcludePatterns($patterns): self
    {
        $this->excludePatterns = (array) $patterns;

        return $this;
    }

    public function getCleanWhen(): array
    {
        return $this->cleanWhen;
    }

    /**
     * Sets whether the directory is cleaned depending on the build state: success,
     * unstable, failure, notBuilt and aborted (defaults to true for all)
     */
    public function setCleanWhen(array $cleanWhen): self
    {
        $this->cleanWhen = $cleanWhen;

        return $this;
    }

    public function getMatchDirectories(): bool
    {
        return $this->matchDirectories;
    }

    /**
     * Sets whether the patterns are applied on directories (defaults to false)
     */
    public function setMatchDirectories(bool $matchDirectories): self
    {
        $this->matchDirectories = $matchDirectories;

        return $this;
    }

    public function getFailBuild(): bool
    {
        return $this->failBuild;
    }

    /**
     * Sets whether the build fails when the cleanup fails (defaults to false)
     */
    public function setFailBuild(bool $failBuild): self
    {
        $this->failBuild = $failBuild;

        return $this;
    }

    public function getCleanupParent(): bool
    {
        return $this->cleanupParent;
    }

    /**
     * Sets whether the matrix parent workspace is cleaned (defaults to false)
     */
    public function setCleanupParent(bool $cleanupParent): self
    {
        $this->cleanupParent = $cleanupParent;

        return $this;
    }

    public function getExternalCommand(): ?string
    {
        return $this->externalCommand;
    }

    /**
     * Sets an external command to cleanup the workspace
     */
    public function setExternalCommand(?string $externalCommand): self
    {
        $this->externalCommand = $externalCommand;

        return $this;
    }

    public function getDeferredWipeout(): bool
    {
        return $this->deferredWipeout;
    }

    /**
     * Sets whether to use deferred wipeout (defaults to true)
     */
    public function setDeferredWipeout(bool $deferredWipeout): self
    {
        $this->deferredWipeout = $deferredWipeout;

        return $this;
    }
}
