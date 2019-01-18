<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Model\Build\BuildInterface;

class AbstractBuildableJob extends AbstractJob implements BuildableJobInterface
{
    public function getRecentBuilds()
    {

    }

    public function getLastBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastBuild']['number']);
    }

    public function getLastCompletedBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastCompletedBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastCompletedBuild']['number']);
    }

    public function getLastFailedBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastFailedBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastFailedBuild']['number']);
    }

    public function getLastStableBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastStableBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastStableBuild']['number']);
    }

    public function getLastSuccessfulBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastSuccessfulBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastSuccessfulBuild']['number']);
    }

    public function getLastUnstableBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastUnstableBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastUnstableBuild']['number']);
    }

    public function getLastUnsuccessfulBuild(): ?BuildInterface
    {
        if (!isset($this->data['lastUnsuccessfulBuild']['number'])) {
            return null;
        }

        return $this->jenkins->builds->get($this, $this->data['lastUnsuccessfulBuild']['number']);
    }
}
