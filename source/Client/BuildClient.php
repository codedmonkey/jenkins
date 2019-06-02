<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Client;

use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Build\BuildInterface;
use CodedMonkey\Jenkins\Model\BuildFactory;
use CodedMonkey\Jenkins\Model\Job\JobInterface;

class BuildClient extends AbstractClient
{
    const RETURN_RESPONSE = 1;

    private $buildFactory;
    private $builds = [];

    public function __construct(Jenkins $jenkins)
    {
        parent::__construct($jenkins);

        $this->buildFactory = new BuildFactory($jenkins);
    }

    /**
     * @return BuildInterface|array
     */
    public function get($job, int $buildNumber, int $flags = 0)
    {
        if (!$job instanceof JobInterface) {
            $job = $this->jenkins->jobs->get($job);
        }

        $jobName = $job->getFullName();

        if ($flags ^ self::RETURN_RESPONSE && isset($this->builds[$jobName][$buildNumber])) {
            return $this->builds[$jobName][$buildNumber];
        }

        $urlPrefix = JobClient::getApiPath($jobName);
        $url = $buildNumber . '/api/json';

        $data = $this->jenkins->request($urlPrefix . $url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        $build = $this->buildFactory->create($job, $buildNumber, $data, true);

        return $this->builds[$jobName][$buildNumber] = $build;
    }

    public function all($job)
    {
    }

    public function register($job, int $buildNumber, array $data): BuildInterface
    {
        if (!$job instanceof JobInterface) {
            $job = $this->jenkins->jobs->get($job);
        }

        $jobName = $job->getFullName();

        if (isset($this->builds[$jobName][$buildNumber])) {
            return $this->builds[$jobName][$buildNumber];
        }

        $build = $this->buildFactory->create($job, $buildNumber, $data);

        return $this->builds[$jobName][$buildNumber] = $build;
    }

    public function getConsoleText($job, int $buildNumber)
    {
        if (!$job instanceof JobInterface) {
            $job = $this->jenkins->jobs->get($job);
        }

        $jobName = $job->getFullName();

        $urlPrefix = JobClient::getApiPath($jobName);
        $url = $buildNumber . '/consoleText';

        $data = $this->jenkins->request($urlPrefix . $url);

        return $data;
    }
}
