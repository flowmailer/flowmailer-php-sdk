<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepSetHeader.
 */
class FlowStepSetHeader implements ModelInterface
{
    /**
     * Name of the header to add to the email.
     */
    private string $headerName;

    /**
     * Value to set in the header.
     */
    private ?string $headerValue = null;

    public function setHeaderName(string $headerName): self
    {
        $this->headerName = $headerName;

        return $this;
    }

    public function getHeaderName(): string
    {
        return $this->headerName;
    }

    public function setHeaderValue(?string $headerValue = null): self
    {
        $this->headerValue = $headerValue;

        return $this;
    }

    public function getHeaderValue(): ?string
    {
        return $this->headerValue;
    }
}
