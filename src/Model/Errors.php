<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\ErrorCollection;

/**
 * Errors.
 */
class Errors implements ModelInterface
{
    /**
     * List of errors.
     */
    private ?ErrorCollection $allErrors = null;

    public function setAllErrors(?ErrorCollection $allErrors = null): self
    {
        $this->allErrors = $allErrors;

        return $this;
    }

    public function getAllErrors(): ?ErrorCollection
    {
        return $this->allErrors;
    }
}
