<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum FlowStepType: string implements \JsonSerializable
{
    case ADD_ATTACHMENT    = 'addAttachment';
    case ADD_HEADER        = 'addHeader';
    case AGGREGATE         = 'aggregate';
    case ANALYTICS         = 'analytics';
    case ARCHIVE           = 'archive';
    case EXTERNAL_CONTENT  = 'externalContent';
    case EXTERNAL_DATA     = 'externalData';
    case EXTRACTDATA       = 'extractdata';
    case MAIL_PLUS_CONTACT = 'mailPlusContact';
    case QAMAIL            = 'qamail';
    case RESUBMIT_MESSAGE  = 'resubmitMessage';
    case REWRITE_RECIPIENT = 'rewriteRecipient';
    case SCHEDULE          = 'schedule';
    case SET_SENDER        = 'setSender';
    case SUBJECT           = 'subject';
    case TEMPLATE          = 'template';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
