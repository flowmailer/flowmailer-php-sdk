<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum ContentDisposition: string implements \JsonSerializable
{
    case ATTACHMENT = 'attachment';
    case INLINE     = 'inline';
    case RELATED    = 'related';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
