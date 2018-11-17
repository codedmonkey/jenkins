<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Client;

use CodedMonkey\Jenkins\Jenkins;

class AbstractClient
{
    protected $jenkins;

    public function __construct(Jenkins $jenkins)
    {
        $this->jenkins = $jenkins;
    }
}
