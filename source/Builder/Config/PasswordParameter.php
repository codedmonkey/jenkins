<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

class PasswordParameter implements ParameterInterface
{
    private $name;
    private $description;
    private $defaultValue = '';

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

    public function getDefaultValue(): string
    {
        return $this->defaultValue ?: '';
    }

    /**
     * Sets the default value of the parameter (defaults to an empty string)
     */
    public function setDefaultValue($defaultValue): ParameterInterface
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }
}
