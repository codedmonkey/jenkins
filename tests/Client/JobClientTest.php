<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Tests\Client;

use CodedMonkey\Jenkins\Client\JobClient;
use CodedMonkey\Jenkins\Jenkins;
use CodedMonkey\Jenkins\Model\Job\FolderJob;
use CodedMonkey\Jenkins\Model\Job\FreestyleJob;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;

class JobClientTest extends TestCase
{
    /** @var JobClient */
    private $client;
    /** @var MockClient */
    private $httpClient;
    /** @var Jenkins */
    private $jenkins;

    protected function setUp()
    {
        $this->httpClient = new MockClient();
        $this->jenkins = new Jenkins($_ENV['JENKINS_URI'], $this->httpClient);
        $this->client = $this->jenkins->jobs;
    }

    public function testFolderJob()
    {
        $response = new Response(200, [], file_get_contents(dirname(__DIR__) . '/Fixtures/jobs/folder-job.json'));
        $this->httpClient->addResponse($response);

        $job = $this->client->get('folder-job');

        $this->assertInstanceOf(FolderJob::class, $job);
        $this->assertSame('folder-job', $job->getName());
        $this->assertSame('folder-job', $job->getFullName());
        $this->assertSame('Folder Job', $job->getDisplayName());
        $this->assertSame('Folder Job', $job->getFullDisplayName());
        $this->assertSame('An example of a folder in Jenkins', $job->getDescription());
    }

    public function testFreestyleJob()
    {
        $response = new Response(200, [], file_get_contents(dirname(__DIR__) . '/Fixtures/jobs/freestyle-job.json'));
        $this->httpClient->addResponse($response);

        $job = $this->client->get('freestyle-job');

        $this->assertInstanceOf(FreestyleJob::class, $job);
        $this->assertSame('freestyle-job', $job->getName());
        $this->assertSame('freestyle-job', $job->getFullName());
        $this->assertSame('Freestyle Job', $job->getDisplayName());
        $this->assertSame('Freestyle Job', $job->getFullDisplayName());
        $this->assertSame('An example of a job in Jenkins', $job->getDescription());
    }
}
