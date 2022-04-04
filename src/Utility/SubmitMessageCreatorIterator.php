<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Utility;

use Flowmailer\API\Model\SubmitMessage;

class SubmitMessageCreatorIterator implements \Iterator
{
    private $callback;

    public function __construct(
        private readonly \Iterator $innerIterator,
        callable $callback
    ) {
        $this->callback      = $callback;
    }

    private function getInnerIterator(): \Iterator
    {
        return $this->innerIterator;
    }

    public function current(): SubmitMessage
    {
        $data = $this->getInnerIterator()->current();

        return \call_user_func($this->callback, $data);
    }

    public function next(): void
    {
        $this->getInnerIterator()->next();
    }

    public function key(): mixed
    {
        return $this->getInnerIterator()->key();
    }

    public function valid(): bool
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind(): void
    {
        $this->getInnerIterator()->rewind();
    }
}
