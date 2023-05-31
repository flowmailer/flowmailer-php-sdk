<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum Protocol: string implements \JsonSerializable
{
    case SMTP        = 'SMTP';
    case SMTP_RCPT   = 'SMTP_RCPT';
    case SMTP_HEADER = 'SMTP_HEADER';
    case SMTP_IPONLY = 'SMTP_IPONLY';
    case SMPP        = 'SMPP';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
