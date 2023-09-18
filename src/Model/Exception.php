<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * Exception.
 */
class Exception implements ModelInterface
{
    private ?string $message = null;

    private ?string $type = null;

    public function setMessage(?string $message = null): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setType(?string $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
