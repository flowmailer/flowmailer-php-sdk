<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum SourceType: string implements \JsonSerializable
{
    case API         = 'API';
    case SMTP        = 'SMTP';
    case SMTP_RCPT   = 'SMTP_RCPT';
    case SMTP_DOMAIN = 'SMTP_DOMAIN';
    case SMPP        = 'SMPP';
    case FLOWMAILER  = 'FLOWMAILER';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
