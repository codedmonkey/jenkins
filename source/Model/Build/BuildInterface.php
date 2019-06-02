<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Model\Build;

interface BuildInterface
{
    public function getDisplayName();

    public function getFullDisplayName();

    public function getDescription();

    public function getUrl();

    public function isBuilding();

    public function getDuration();

    public function getConsoleText();

    public function refresh(): void;
}
