<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class ShellBuilder implements BuilderInterface
{
    private $command;

    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Sets the command to execute in the shell
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }
}
