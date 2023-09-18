<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * Header.
 */
class Header implements ModelInterface
{
    public function __construct(
        /**
         * Header name.
         */
        private string $name,
        /**
         * Header value.
         */
        private ?string $value = null
    ) {
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue(?string $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
