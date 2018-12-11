<?php
/*
 * This file is part of the Onlinq library.
 *
 * (c) Onlinq <info@onlinq.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Codedmonkey\Jenkins\Tests;

use CodedMonkey\Jenkins\Builder\JobConfigBuilder;
use PHPUnit\Framework\TestCase;

class JobConfigBuilderTest extends TestCase
{
    /** @var JobConfigBuilder */
    protected $builder;

    protected function setUp()
    {
        $this->builder = new JobConfigBuilder();
    }

    public function testFolder()
    {
        $config = $this->builder
            ->setType(JobConfigBuilder::TYPE_FOLDER)
            ->buildConfig()
        ;

        $this->assertEquals($this->getFixture('folder'), $config);
    }

    protected function getFixture(string $name): string
    {
        return file_get_contents(__DIR__ . '/Fixtures/' . $name . '.xml');
    }
}
