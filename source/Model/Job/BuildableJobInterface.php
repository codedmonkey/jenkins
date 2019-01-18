<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

use CodedMonkey\Jenkins\Model\Build\BuildInterface;

interface BuildableJobInterface extends JobInterface
{
    public function getLastBuild(): ?BuildInterface;

    public function getLastCompletedBuild(): ?BuildInterface;

    public function getLastFailedBuild(): ?BuildInterface;

    public function getLastStableBuild(): ?BuildInterface;

    public function getLastSuccessfulBuild(): ?BuildInterface;

    public function getLastUnstableBuild(): ?BuildInterface;

    public function getLastUnsuccessfulBuild(): ?BuildInterface;
}
