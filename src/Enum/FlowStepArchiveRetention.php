<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum FlowStepArchiveRetention: string implements \JsonSerializable
{
    case P1M = 'P1M';
    case P3M = 'P3M';
    case P6M = 'P6M';
    case P1Y = 'P1Y';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
