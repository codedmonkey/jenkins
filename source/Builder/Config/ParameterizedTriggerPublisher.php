<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class ParameterizedTriggerPublisher implements PublisherInterface
{
    private $jobs;
    private $predefinedParameters = [];
    private $currentParameters = false;
    private $currentNode = false;
    private $condition = 'SUCCESS';
    private $triggerWithNoParameters = false;
    private $triggerFromChildProjects = false;

    public function getJobs(): array
    {
        return $this->jobs;
    }

    /**
     * Sets the jobs to trigger
     *
     * @param array|string $jobs
     */
    public function setJobs($jobs): self
    {
        $this->jobs = (array) $jobs;

        return $this;
    }

    public function getPredefinedParameters(): array
    {
        return $this->predefinedParameters;
    }

    /**
     * Sets parameters to pass to the triggered jobs
     */
    public function setPredefinedParameters(array $predefinedParameters): self
    {
        $this->predefinedParameters = $predefinedParameters;

        return $this;
    }

    public function getCurrentParameters(): bool
    {
        return $this->currentParameters;
    }

    /**
     * Sets whether to include parameters passed to the current build to the triggered jobs
     */
    public function setCurrentParameters(bool $currentParameters): self
    {
        $this->currentParameters = $currentParameters;

        return $this;
    }

    public function getCurrentNode(): bool
    {
        return $this->currentNode;
    }

    /**
     * Sets whether to use the same node for the triggered builds that was used for this build
     */
    public function setCurrentNode(bool $currentNode): self
    {
        $this->currentNode = $currentNode;

        return $this;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * Sets when the trigger should be activated: SUCCESS, UNSTABLE, FAILED_OR_BETTER,
     * UNSTABLE_OR_BETTER, UNSTABLE_OR_WORSE, FAILED or ALWAYS (defaults to SUCCESS)
     */
    public function setCondition(string $condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    public function getTriggerWithNoParameters(): bool
    {
        return $this->triggerWithNoParameters;
    }

    /**
     * Sets whether the trigger is activated when there are no parameters defined (defaults to false)
     */
    public function setTriggerWithNoParameters(bool $triggerWithNoParameters): self
    {
        $this->triggerWithNoParameters = $triggerWithNoParameters;

        return $this;
    }

    public function getTriggerFromChildProjects(): bool
    {
        return $this->triggerFromChildProjects;
    }

    /**
     * Sets whether to trigger the build from child projects. Used for matrix projects. (defaults to false)
     */
    public function setTriggerFromChildProjects(bool $triggerFromChildProjects): self
    {
        $this->triggerFromChildProjects = $triggerFromChildProjects;

        return $this;
    }
}
