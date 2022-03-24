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
    private \Iterator $innerIterator;
    private $callback;

    public function __construct(\Iterator $innerIterator, callable $callback)
    {
        $this->innerIterator = $innerIterator;
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

    public function next(): ?SubmitMessage
    {
        return $this->getInnerIterator()->next();
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind()
    {
        return $this->getInnerIterator()->rewind();
    }
}
