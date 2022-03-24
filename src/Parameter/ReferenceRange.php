<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Parameter;

class ReferenceRange
{
    private int $count;
    private ?string $reference;

    public function __construct(?string $reference = null, int $count)
    {
        $this->reference = $reference;
        $this->count     = $count;
    }

    public function __toString()
    {
        return sprintf('items=%s:%d', $this->getReference(), $this->getCount());
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

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): ReferenceRange
    {
        $this->count = $count;

        return $this;
    }
}
