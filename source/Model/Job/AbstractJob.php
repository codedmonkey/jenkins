<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Jenkins;

class AbstractJob
{
    private $client;
    private $data;

    private $initialized;

    public function __construct(array $data, bool $initialized = false, Jenkins $client = null)
    {
        $this->data = $data;
        $this->initialized = $initialized;

        $this->client = $client;
    }

    public function getDisplayName()
    {
        return $this->data['displayName'];
    }

    public function getFullDisplayName()
    {
        return $this->data['fullDisplayName'];
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getFullName()
    {
        return $this->data['fullName'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getUrl()
    {
        return $this->data['url'];
    }
}
