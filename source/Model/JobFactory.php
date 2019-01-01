<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model;

use CodedMonkey\Jenkins\Exception\ModelException;
use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Job\FolderJob;
use CodedMonkey\Jenkins\Model\Job\FreestyleJob;
use CodedMonkey\Jenkins\Model\Job\Job;

class JobFactory
{
    private $jenkins;

    public function __construct(Jenkins $jenkins)
    {
        $this->jenkins = $jenkins;
    }

    public function create(array $data, bool $initialized = false)
    {
        $type = $data['_class'] ?? false;

        if (!$type) {
            throw new ModelException('No job type specified.');
        }

        static $typeMap = [
            'hudson.model.FreeStyleProject' => FreestyleJob::class,
            'com.cloudbees.hudson.plugins.folder.Folder' => FolderJob::class,
        ];

        $class = $typeMap[$type] ?? Job::class;

        return new $class($this->jenkins, $data, $initialized);
    }
}
