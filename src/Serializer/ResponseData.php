<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Serializer;

class ResponseData implements \Stringable
{
    public function __construct(
        private readonly string $responseBody,
        private readonly array $meta = [],
    ) {
    }

    public function __toString(): string
    {
        return $this->responseBody;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getMeta($key): mixed
    {
        return $this->meta[$key] ?? null;
    }
}
