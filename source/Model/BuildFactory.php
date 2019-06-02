<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model;

use CodedMonkey\Jenkins\Exception\ModelException;
use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Build\Build;
use CodedMonkey\Jenkins\Model\Build\BuildInterface;
use CodedMonkey\Jenkins\Model\Build\FreestyleBuild;
use CodedMonkey\Jenkins\Model\Job\JobInterface;

class BuildFactory
{
    private $jenkins;

    public function __construct(Jenkins $jenkins)
    {
        $this->jenkins = $jenkins;
    }

    public function create(JobInterface $job, int $buildNumber, array $data, bool $initialized = false): BuildInterface
    {
        $type = $data['_class'] ?? false;

        if (!$type) {
            throw new ModelException('No build type specified.');
        }

        static $typeMap = [
            'hudson.model.FreeStyleBuild' => FreestyleBuild::class,
        ];

        $class = $typeMap[$type] ?? Build::class;

        return new $class($this->jenkins, $job, $buildNumber, $data, $initialized);
    }
}
