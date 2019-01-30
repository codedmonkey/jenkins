<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Client\JobClient;
use CodedMonkey\Jenkins\Model\Build\BuildInterface;

abstract class AbstractBuildableJob extends AbstractJob implements BuildableJobInterface
{
    /** @var array|bool */
    private $allBuilds = false;

    public function getRecentBuilds(): array
    {
        $builds = $this->getData('builds');

        return array_map(function (array $buildData) {
            return $this->jenkins->builds->get($this, $buildData['number']);
        }, $builds);
    }

    public function getAllBuilds(): array
    {
        if (false === $this->allBuilds) {
            $urlPrefix = JobClient::getApiPath($this->getFullName());
            $url = 'api/json?tree=allBuilds[number]';

            $data = $this->jenkins->request($urlPrefix . $url);
            $data = json_decode($data, true);

            $this->allBuilds = $data['allBuilds'];

            array_map(function (array $buildData) {
                if (!isset($buildData['_class']) || !isset($buildData['number'])) {
                    return;
                }

                $this->jenkins->builds->register($this, $buildData['number'], $buildData);
            }, $this->allBuilds);
        }

        return array_map(function (array $buildData) {
            return $this->jenkins->builds->get($this, $buildData['number']);
        }, $this->allBuilds);
    }

    public function getLastBuild(): ?BuildInterface
    {
        $lastBuild = $this->getData('lastBuild');
        $buildNumber = $lastBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastCompletedBuild(): ?BuildInterface
    {
        $lastCompletedBuild = $this->getData('lastCompletedBuild');
        $buildNumber = $lastCompletedBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastFailedBuild(): ?BuildInterface
    {
        $lastFailedBuild = $this->getData('lastFailedBuild');
        $buildNumber = $lastFailedBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastStableBuild(): ?BuildInterface
    {
        $lastStableBuild = $this->getData('lastStableBuild');
        $buildNumber = $lastStableBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastSuccessfulBuild(): ?BuildInterface
    {
        $lastSuccessfulBuild = $this->getData('lastSuccessfulBuild');
        $buildNumber = $lastSuccessfulBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastUnstableBuild(): ?BuildInterface
    {
        $lastUnstableBuild = $this->getData('lastUnstableBuild');
        $buildNumber = $lastUnstableBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }

    public function getLastUnsuccessfulBuild(): ?BuildInterface
    {
        $lastUnsuccessfulBuild = $this->getData('lastUnsuccessfulBuild');
        $buildNumber = $lastUnsuccessfulBuild['number'] ?? false;

        if (!$buildNumber) {
            return null;
        }

        return $this->jenkins->builds->get($this, $buildNumber);
    }
}
