<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * NamedSample.
 */
final class NamedSample implements ModelInterface
{
    private string $name;

    private ?bool $other = null;

    private ?int $value = null;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setOther(?bool $other = null): self
    {
        $this->other = $other;

        return $this;
    }

    public function getOther(): ?bool
    {
        return $this->other;
    }

    public function setValue(?int $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
