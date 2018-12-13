<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

class BooleanParameterDumper extends AbstractParameterDumper
{
    protected function getNodeName(): string
    {
        return 'hudson.model.BooleanParameterDefinition';
    }

    protected function dumpDefaultValue($defaultValue): string
    {
        return $defaultValue ? 'true' : 'false';
    }
}
