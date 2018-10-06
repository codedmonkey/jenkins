<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model;

use CodedMonkey\Jenkins\Exception\RuntimeException;
use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Job\FreestyleJob;

class JobFactory
{
    /**
     * @var Jenkins
     */
    private $client;

    public function __construct(Jenkins $client)
    {
        $this->client = $client;
    }

    public function create(array $data)
    {
        $type = $data['_class'] ?? false;

        if (!$type) {
            throw new RuntimeException('No job type specified.');
        }

        static $typeMap = [
            'hudson.model.FreeStyleProject' => FreestyleJob::class,
            'com.cloudbees.hudson.plugins.folder.Folder' => FolderJob::class,
        ];

        $class = $typeMap[$type] ?? false;

        if (!$class) {
            throw new RuntimeException(sprintf('Unknown job type "%s".', $type));
        }

        return new $class($data, $this->client);
    }
}
