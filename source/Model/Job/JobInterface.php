<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Job;

interface JobInterface
{
    public function getDisplayName();

    public function getFullDisplayName();

    public function getName();

    public function getFullName();

    public function getDescription();

    public function getUrl();

    public function getConfig();

    public function refresh(): void;

    public function delete(): void;
}
