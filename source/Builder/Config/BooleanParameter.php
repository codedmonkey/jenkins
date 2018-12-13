<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class BooleanParameter implements ParameterInterface
{
    private $name;
    private $description;
    private $defaultValue = false;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the parameter
     */
    public function setName(string $name): ParameterInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the parameter
     */
    public function setDescription(?string $description): ParameterInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getDefaultValue(): bool
    {
        return (bool) $this->defaultValue;
    }

    /**
     * Sets the default value of the parameter (defaults to false)
     */
    public function setDefaultValue($defaultValue): ParameterInterface
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }
}
