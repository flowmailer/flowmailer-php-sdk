<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum TemplateEngine: string implements \JsonSerializable
{
    case FREEMARKER_2_3_20 = 'freemarker-2.3.20';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
