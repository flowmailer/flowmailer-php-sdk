<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum DnsRecordStatus: string implements \JsonSerializable
{
    /*
     * We had an error while validating this DNS record
     */
    case UNKNOWN = 'UNKNOWN';

    /*
     * DNS record is not correct WARNING:
     */
    case ERROR   = 'ERROR';

    /*
     * record is functional but could be improved
     */
    case DNS     = 'DNS';

    /*
     * DNS record is ok
     */
    case OK      = 'OK';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
