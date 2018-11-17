<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins;

use CodedMonkey\Jenkins\Client\JobClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * @property JobClient $jobs
 */
class Jenkins
{
    private static $clientClasses = [
        'jobs' => JobClient::class,
    ];

    private $httpClient;
    private $requestFactory;

    private $url;
    private $clients = [];

    public function __construct(string $url, HttpClient $httpClient = null, MessageFactory $requestFactory = null)
    {
        $this->url = $url;
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function __get($name)
    {
        return $this->getClient($name);
    }

    private function getClient($name)
    {
        if (!isset($this->clients[$name])) {
            if (!isset(self::$clientClasses[$name])) {
                throw new \Exception(sprintf('Unknown API client "%s".'. $name));
            }

            $this->clients[$name] = new self::$clientClasses[$name]($this, $this->httpClient, $this->requestFactory);
        }

        return $this->clients[$name];
    }

    public function request(string $url): string
    {
        $url = $this->url . '/' . $url;

        $request = $this->requestFactory->createRequest('get', $url);
        $response = $this->httpClient->sendRequest($request);

        $this->validateResponse($response);

        return $response->getBody();
    }

    private function validateResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
            return;
        }

        throw new \RuntimeException;
    }
}
