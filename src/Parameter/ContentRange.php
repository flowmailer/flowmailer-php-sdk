<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Parameter;

class ContentRange implements \Stringable
{
    public function __construct(
        private string $startReference,
        private string $endReference,
        private int|string|null $total = null,
    ) {
    }

    public function __toString(): string
    {
        $separator = (is_null($this->getTotal()) || $this->getTotal() == '*') ? ':' : '-';

        return sprintf('items %s%s%s/%s', $this->getStartReference(), $separator, $this->getEndReference(), $this->getTotal() ?? '*');
    }

    public static function fromString(string $string): self
    {
        if (str_ends_with($string, '/*')) {
            $total         = '*';
            [$start, $end] = explode(':', substr($string, 6, -2));
        } else {
            [$ranges, $total] = explode('/', substr($string, 6));
            [$start, $end]    = explode('-', $ranges);
        }

        return new self($start, $end, $total);
    }

    public function getStartReference(): string
    {
        return $this->startReference;
    }

    public function setStartReference(string $startReference): ContentRange
    {
        $this->startReference = $startReference;

        return $this;
    }

    public function getEndReference(): string
    {
        return $this->endReference;
    }

    public function setEndReference(string $endReference): ContentRange
    {
        $this->endReference = $endReference;

        return $this;
    }

    public function getTotal(): int|string|null
    {
        return $this->total;
    }

    public function setTotal(int|string|null $total): ContentRange
    {
        $this->total = $total;

        return $this;
    }
}
