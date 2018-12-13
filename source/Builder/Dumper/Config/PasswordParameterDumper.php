<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Dumper\Config;

class PasswordParameterDumper extends AbstractParameterDumper
{
    protected function getNodeName(): string
    {
        return 'hudson.model.PasswordParameterDefinition';
    }

    protected function dumpDefaultValue($defaultValue): string
    {
        return (string) $defaultValue;
    }
}
