<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model;

class Job
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getType()
    {
        return $this->data['_class'];
    }

    public function getDisplayName()
    {
        return $this->data['displayName'];
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getUrl()
    {
        return $this->data['url'];
    }

    public function getBuildable()
    {
        return $this->data['buildable'];
    }

    public function getColor()
    {
        return $this->data['color'];
    }

    public function getInQueue()
    {
        return $this->data['inQueue'];
    }

    public function getKeepDependencies()
    {
        return $this->data['keepDependencies'];
    }

    public function getNextBuildNumber()
    {
        return $this->data['nextBuildNumber'];
    }

    public function getQueueItem()
    {
        return $this->data['queueItem'];
    }

    public function getConcurrentBuild()
    {
        return $this->data['concurrentBuild'];
    }

    public function getLabelExpression()
    {
        return $this->data['labelExpression'];
    }

    public function getScmType()
    {
        return $this->data['scm']['_class'];
    }

    public function getActions()
    {
        // todo
    }

    public function getProperty()
    {
        // todo
    }

    public function getDownstreamProjects()
    {
        // todo
    }

    public function getUpstreamProjects()
    {
        // todo
    }
}
