<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * TemplatePreview.
 */
final class TemplatePreview implements ModelInterface
{
    private ?string $error = null;

    private ?int $errorLine = null;

    private ?string $result = null;

    public function setError(?string $error = null): self
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setErrorLine(?int $errorLine = null): self
    {
        $this->errorLine = $errorLine;

        return $this;
    }

    public function getErrorLine(): ?int
    {
        return $this->errorLine;
    }

    public function setResult(?string $result = null): self
    {
        $this->result = $result;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }
}
