<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Client;

use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Job\FolderJob;
use CodedMonkey\Jenkins\Model\JobFactory;

class JobClient extends AbstractClient
{
    const RETURN_RESPONSE = 1;
    const INITIALIZE_JOBS = 2;
    const RESOLVE_FOLDERS = 4;

    private $jobFactory;

    public function __construct(Jenkins $jenkins)
    {
        parent::__construct($jenkins);

        $this->jobFactory = new JobFactory($jenkins);
    }

    public function create(string $name, ?string $folder, string $configuration)
    {
        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = sprintf('createItem?name=%s', $name);

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ];

        $data = $this->jenkins->post($urlPrefix . $url, $configuration, $options);

        return $data;
    }

    public function update(string $name, ?string $folder, string $configuration)
    {
        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'config.xml';

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ];

        $data = $this->jenkins->post($urlPrefix . $url, $configuration, $options);

        return $data;
    }

    public function get(string $name, ?string $folder = null, $flags = 0)
    {
        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'api/json';

        $data = $this->jenkins->request($urlPrefix . $url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        return $this->jobFactory->create($data, true);
    }

    public function all(?string $folder = null, $flags = 0)
    {
        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = 'api/json?tree=jobs[_class,name,fullName]';

        $data = $this->jenkins->request($urlPrefix . $url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        $jobs = [];

        foreach ($data['jobs'] as $jobData) {
            $initialized = false;

            if ($flags & self::INITIALIZE_JOBS) {
                $jobData = $this->get($jobData['name'], $folder,self::RETURN_RESPONSE);
                $initialized = true;
            }

            $job = $this->jobFactory->create($jobData, $initialized);

            if ($job instanceof FolderJob && $flags & self::RESOLVE_FOLDERS) {
                $nestedJobs = $this->all($job->getFullName(), $flags);

                // Append the nested jobs and avoid adding the folder as a job
                array_push($jobs, ...$nestedJobs);
                continue;
            }

            $jobs[] = $job;
        }

        return $jobs;
    }

    private function getApiPath(string $job)
    {
        $parts = explode('/', $job);

        return implode('', array_map(function($part) {
            return sprintf('job/%s/', $part);
        }, $parts));
    }
}
