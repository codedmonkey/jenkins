<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins;

use CodedMonkey\Jenkins\Model\Job;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;

class Jenkins
{
    private $httpClient;
    private $requestFactory;

    private $url;

    public function __construct(string $url, $httpClient = null, $requestFactory = null)
    {
        $this->url = $url;
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function getJob(string $name)
    {
        $request = $this->requestFactory->createRequest('get', sprintf('%s/job/%s/api/json', $this->url, $name));
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
