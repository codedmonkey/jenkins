<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

class StringParameterDumper extends AbstractParameterDumper
{
    protected function getNodeName(): string
    {
        return 'hudson.model.StringParameterDefinition';
    }

    protected function dumpDefaultValue($defaultValue): string
    {
        return (string) $defaultValue;
    }
}
