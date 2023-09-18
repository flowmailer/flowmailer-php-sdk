<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * Sample.
 */
class Sample implements ModelInterface
{
    private ?\DateTimeInterface $timestamp = null;

    private ?int $value = null;

    public function setTimestamp(?\DateTimeInterface $timestamp = null): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
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
