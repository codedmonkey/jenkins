<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Client;

use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\JobFactory;

class JobClient extends AbstractClient
{
    const RETURN_RESPONSE = 1;
    const INITIALIZE_JOBS = 2;
    const RESOLVE_FOLDERS = 3;

    private $jobFactory;

    public function __construct(Jenkins $jenkins)
    {
        parent::__construct($jenkins);

        $this->jobFactory = new JobFactory($jenkins);
    }

    public function get(string $name, $flags = 0)
    {
        $name = str_replace('/', '/job/', $name);
        $url = sprintf('job/%s/api/json', $name);

        $data = $this->jenkins->request($url);
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        return $this->jobFactory->create($data, true);
    }

    public function all(string $folder = null, $flags = 0)
    {
        $data = $this->jenkins->request('api/json?tree=jobs[_class,name,fullName]');
        $data = json_decode($data, true);

        if ($flags & self::RETURN_RESPONSE) {
            return $data;
        }

        $jobs = [];

        foreach ($data['jobs'] as $jobData) {
            $initialized = false;

            if ($flags & self::INITIALIZE_JOBS) {
                $jobData = $this->get($jobData['fullName'], self::RETURN_RESPONSE);
                $initialized = true;
            }

            $jobs[] = $this->jobFactory->create($jobData, $initialized);
        }

        return $jobs;
    }
}
