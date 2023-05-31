<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum TemplateMimeType: string implements \JsonSerializable
{
    case PLAIN = 'text/plain';
    case HTML  = 'text/html';
    case PDF   = 'application/vnd.flowmailer.itext+pdf';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
