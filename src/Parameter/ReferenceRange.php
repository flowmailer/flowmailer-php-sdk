<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Parameter;

class ReferenceRange implements \Stringable
{
    public function __construct(
        private int $count,
        private ?string $reference = null,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('items=%s:%d', $this->getReference(), $this->getCount());
    }

    public static function fromString(string $string): self
    {
        parse_str($string, $data);
        $values = explode(':', (string) $data['items']);

        return new self((int) $values[1], $values[0]);
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): ReferenceRange
    {
        $this->count = $count;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): ReferenceRange
    {
        $this->reference = $reference;

        return $this;
    }
}
