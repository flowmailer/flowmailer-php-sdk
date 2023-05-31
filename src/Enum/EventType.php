<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum EventType: string implements \JsonSerializable
{
    case SOFTBOUNCE      = 'SOFTBOUNCE';
    case HARDBOUNCE      = 'HARDBOUNCE';
    case COMPLAINT       = 'COMPLAINT';
    case UNSUBSCRIBE     = 'UNSUBSCRIBE';
    case REJECT          = 'REJECT';
    case DISCARD         = 'DISCARD';
    case DELIVERED       = 'DELIVERED';
    case POSTED          = 'POSTED';
    case ATTEMPT         = 'ATTEMPT';
    case QUEUEATTEMPT    = 'QUEUEATTEMPT';
    case SUBMITTED       = 'SUBMITTED';
    case PROCESSED       = 'PROCESSED';
    case INSERTED        = 'INSERTED';
    case OPENED          = 'OPENED';
    case CLICKED         = 'CLICKED';
    case HELD            = 'HELD';
    case CUSTOM          = 'CUSTOM';
    case REPLY           = 'REPLY';
    case RESENT          = 'RESENT';
    case SUSPENDED       = 'SUSPENDED';
    case RESUBMITTED     = 'RESUBMITTED';
    case ERROR           = 'ERROR';
    case RESUMED         = 'RESUMED';
    case EVENT_SUSPENDED = 'EVENT_SUSPENDED';
    case EVENT_ERROR     = 'EVENT_ERROR';
    case EVENT_RESUMED   = 'EVENT_RESUMED';
    case EVENT_DISCARD   = 'EVENT_DISCARD';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
