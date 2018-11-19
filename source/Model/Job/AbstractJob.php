<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Client\JobClient;
use CodedMonkey\Jenkins\Jenkins;

class AbstractJob
{
    protected $jenkins;
    protected $data;
    protected $initialized;

    public function __construct(Jenkins $jenkins, array $data, bool $initialized = false)
    {
        $this->jenkins = $jenkins;
        $this->data = $data;
        $this->initialized = $initialized;
    }

    public function getDisplayName()
    {
        return $this->getData('displayName');
    }

    public function getFullDisplayName()
    {
        return $this->getData('fullDisplayName');
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
        return $this->getData('description');
    }

    public function getUrl()
    {
        return $this->getData('url');
    }

    protected function getData(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->data[$name];
    }

    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $data = $this->jenkins->jobs->get($this->data['name'], JobClient::RETURN_RESPONSE);

        $this->data = $data;
        $this->initialized = true;
    }
}
