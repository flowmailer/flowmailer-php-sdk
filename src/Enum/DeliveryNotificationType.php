<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum DeliveryNotificationType: string implements \JsonSerializable
{
    case NONE                 = 'NONE';
    case FAILURE              = 'FAILURE';
    case DELIVERY_AND_FAILURE = 'DELIVERY_AND_FAILURE';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
