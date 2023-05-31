<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum MessageType: string implements \JsonSerializable
{
    case EMAIL  = 'EMAIL';
    case SMS    = 'SMS';
    case LETTER = 'LETTER';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
