<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Tests;

use CodedMonkey\Jenkins\Jenkins;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;

class JenkinsTest extends TestCase
{
    /** @var MockClient */
    private $httpClient;
    /** @var Jenkins */
    private $jenkins;

    protected function setUp()
    {
        $this->httpClient = new MockClient();
        $this->jenkins = new Jenkins($_ENV['JENKINS_URI'], $this->httpClient);
    }

    /**
     * @expectedException \CodedMonkey\Jenkins\Exception\TransferException
     */
    public function testThrowsTransferException()
    {
        $response = new Response(400);
        $this->httpClient->addResponse($response);

        $this->jenkins->request('job/faulty-job/');
    }
}
