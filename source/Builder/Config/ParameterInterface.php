<?php
/*
 * (c) Tim Goudriaan <tim@codedmonkey.com>
 */

namespace CodedMonkey\Jenkins\Builder\Config;

interface ParameterInterface
{
    public function getName(): string;

    public function setName(string $name): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;

    public function getDefaultValue();

    public function setDefaultValue($defaultValue): self;
}
