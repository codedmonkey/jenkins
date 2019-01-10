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
    const FORCE_FETCH = 1;
    const RETURN_RESPONSE = 2;
    const INITIALIZE_JOBS = 4;
    const RESOLVE_FOLDERS = 8;

    private $jobFactory;
    private $jobs = [];
    private $folders = [];

    public function __construct(Jenkins $jenkins)
    {
        parent::__construct($jenkins);

        $this->jobFactory = new JobFactory($jenkins);
    }

    public function get(string $name, ?string $folder = null, $flags = 0)
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        if ($flags ^ self::FORCE_FETCH && isset($this->jobs[$folder][$name])) {
            return $this->jobs[$folder][$name];
        }

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'api/json';

        $data = $this->jenkins->request($urlPrefix . $url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        $job = $this->jobFactory->create($data, true);

        return $this->jobs[$folder][$name] = $job;
    }

    public function all(?string $folder = null, $flags = 0)
    {
        if ($flags ^ self::FORCE_FETCH && isset($this->folders[$folder])) {
            return $this->jobs[$folder];
        }

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = 'api/json?tree=jobs[_class,name,fullName]';

        $data = $this->jenkins->request($urlPrefix . $url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        $jobs = [];

        foreach ($data['jobs'] as $jobData) {
            $name = $jobData['name'];
            $job = $this->jobs[$folder][$name] ?? $this->jobFactory->create($jobData);

            if ($flags & self::INITIALIZE_JOBS) {
                $job->initialize();
            }

            if ($job instanceof FolderJob && $flags & self::RESOLVE_FOLDERS) {
                $nestedJobs = $this->all($job->getFullName(), $flags);

                // Append the nested jobs and avoid adding the folder as a job
                array_push($jobs, ...$nestedJobs);
                continue;
            }

            $this->jobs[$folder][$name] = $jobs[] = $job;
        }

        $this->folders[$folder] = true;

        return $jobs;
    }

    public function getConfig(string $name, ?string $folder = null, $flags = 0)
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'config.xml';

        $data = $this->jenkins->request($urlPrefix . $url);

        return $data;
    }

    public function build(string $name, ?string $folder = null, array $parameters = [])
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        if (!count($parameters)) {
            $url = $this->getApiPath($name) . 'build';
        }
        else {
            $url = $this->getApiPath($name) . 'buildWithParameters?' . http_build_query($parameters);
        }

        $this->jenkins->post($urlPrefix . $url);
    }

    public function create(string $name, ?string $folder, string $configuration)
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = sprintf('createItem?name=%s', $name);

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ];

        $this->jenkins->post($urlPrefix . $url, $configuration, $options);
    }

    public function update(string $name, ?string $folder, string $configuration)
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'config.xml';

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ];

        $this->jenkins->post($urlPrefix . $url, $configuration, $options);
    }

    public function delete(string $name, ?string $folder = null)
    {
        $shortName = $this->getJobName($name, $folder);
        $folder = $this->getFolderName($name, $folder);
        $name = $shortName;

        $urlPrefix = $folder ? $this->getApiPath($folder) : null;
        $url = $this->getApiPath($name) . 'doDelete';

        $this->jenkins->post($urlPrefix . $url);

        unset($this->jobs[$folder][$name]);
    }

    private function getApiPath(string $job): string
    {
        $parts = explode('/', $job);

        return implode('', array_map(function($part) {
            return sprintf('job/%s/', $part);
        }, $parts));
    }

    private function getJobName(string $name, ?string $folder): string
    {
        $fullName = implode('/', array_filter([$folder, $name]));
        $parts = explode('/', $fullName);

        return array_pop($parts);
    }

    private function getFolderName(string $name, ?string $folder): string
    {
        $fullName = implode('/', array_filter([$folder, $name]));
        $parts = explode('/', $fullName);

        array_pop($parts);

        return implode('/', $parts);
    }
}
