<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Exception;

use Flowmailer\API\Collection\ErrorCollection;

class ClientException extends ApiException
{
    private ErrorCollection $errors;

    public function getErrors(): ErrorCollection
    {
        return $this->errors ?? new ErrorCollection([]);
    }

    public function setErrors(ErrorCollection $errors): ClientException
    {
        $this->errors = $errors;

        return $this;
    }
}
