<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

use CodedMonkey\Jenkins\Builder\Config\ShellBuilder;

class ShellBuilderDumper
{
    public function dump(\DOMDocument $dom, ShellBuilder $builder)
    {
        $node = $dom->createElement('hudson.tasks.Shell');

        $commandNode = $dom->createElement('command', $builder->getCommand());
        $node->appendChild($commandNode);

        return $node;
    }
}
