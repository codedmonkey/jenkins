<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins;

use CodedMonkey\Jenkins\Model\Job;
use CodedMonkey\Jenkins\Model\JobFactory;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;

class Jenkins
{
    private $httpClient;
    private $requestFactory;

    private $jobFactory;

    private $url;

    public function __construct(string $url, $httpClient = null, $requestFactory = null)
    {
        $this->url = $url;
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();

        $this->jobFactory = new JobFactory($this);
    }

    public function setJobFactory($factory): self
    {
        $this->jobFactory = $factory;

        return $this;
    }

    public function getJob(string $name)
    {
        if (strpos($name, '/') && !strpos($name, '/job/')) {
            $name = str_replace('/', '/job/', $name);
        }
        $url = sprintf('%s/job/%s/api/json', $this->url, $name);

        $request = $this->requestFactory->createRequest('get', $url);
        $response = $this->httpClient->sendRequest($request);

        $this->validateResponse($response);

        $data = json_decode($response->getBody(), true);

        return new Job($data);
    }

    private function validateResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
            return;
        }

        throw new \RuntimeException;
    }
}
