<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Client\JobClient;
use CodedMonkey\Jenkins\Exception\ModelException;
use CodedMonkey\Jenkins\Jenkins;

abstract class AbstractJob implements JobInterface
{
    protected $jenkins;

    protected $config;
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

    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }

        $this->config = $this->jenkins->jobs->getConfig($this->getFullName());

        return $this->config;
    }

    protected function getData(string $name)
    {
        if (!isset($this->data[$name])) {
            if (!$this->initialized) {
                $this->initialize();
            }

            if (!isset($this->data[$name])) {
                throw new ModelException(sprintf('Invalid field: %s', $name));
            }
        }

        return $this->data[$name];
    }

    public function refresh(): void
    {
        $data = $this->jenkins->jobs->get($this->data['fullName'], null, JobClient::RETURN_RESPONSE);

        $this->data = $data;
        $this->initialized = true;
    }

    public function delete(): void
    {
        $this->jenkins->jobs->delete($this->data['fullName'], null);
    }

    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->refresh();
    }
}
