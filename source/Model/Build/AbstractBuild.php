<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Build;

use CodedMonkey\Jenkins\Client\BuildClient;
use CodedMonkey\Jenkins\Exception\ModelException;
use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Job\JobInterface;

class AbstractBuild implements BuildInterface
{
    protected $jenkins;
    private $job;

    protected $data;
    protected $initialized;

    public function __construct(Jenkins $jenkins, JobInterface $job, array $data, bool $initialized = false)
    {
        $this->jenkins = $jenkins;
        $this->job = $job;

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

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getUrl()
    {
        return $this->getData('url');
    }

    public function isBuilding()
    {
        return $this->getData('building');
    }

    public function getDuration()
    {
        return $this->getData('duration');
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
        $data = $this->jenkins->builds->get($this->data['fullName'], null, BuildClient::RETURN_RESPONSE);

        $this->data = $data;
        $this->initialized = true;
    }

    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->refresh();
    }
}
