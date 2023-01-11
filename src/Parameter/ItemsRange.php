<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Parameter;

class ItemsRange implements \Stringable
{
    public function __construct(
        private int $start,
        private int $end,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('items=%d-%d', $this->getStart(), $this->getEnd());
    }

    public static function fromString(string $string): self
    {
        parse_str($string, $data);
        [$start, $end] = explode('-', (string) $data['items']);

        return new self((int) $start, (int) $end);
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): ItemsRange
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function setEnd(int $end): ItemsRange
    {
        $this->end = $end;

        return $this;
    }
}
