<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

class FolderJob extends AbstractJob
{
    public function getJobs($flags = 0): array
    {
        return $this->jenkins->jobs->all($this->getFullName(), $flags);
    }
}
