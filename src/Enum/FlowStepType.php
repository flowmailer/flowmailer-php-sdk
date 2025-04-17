<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Enum;

enum FlowStepType: string implements \JsonSerializable
{
    case ADD_ATTACHMENT         = 'addAttachment';
    case ADD_HEADER             = 'addHeader';
    case AGGREGATE              = 'aggregate';
    case ANALYTICS              = 'analytics';
    case ARCHIVE                = 'archive';
    case CLAM_SCAN              = 'clamScan';
    case CLICK_TRACKING         = 'clickTracking';
    case DATA_CACH              = 'dataCach';
    case DISCARD                = 'discard';
    case DNS_LOOKUP             = 'dnsLookup';
    case EXTERNAL_CONTENT       = 'externalContent';
    case EXTERNAL_DATA          = 'externalData';
    case EXTRACTDATA            = 'extractdata';
    case LDAP_SEARCH            = 'ldapSearch';
    case MAIL_PLUS_CONTACT      = 'mailPlusContact';
    case OPEN_TRACKING          = 'openTracking';
    case QAMAIL                 = 'qamail';
    case RESET_MESSAGE          = 'resetMessage';
    case RESUBMIT_MESSAGE       = 'resubmitMessage';
    case REWRITE_RECIPIENT      = 'rewriteRecipient';
    case SCHEDULE               = 'schedule';
    case SELECT_SENDER_IDENTITY = 'selectSenderIdentity';
    case SET_SENDER             = 'setSender';
    case SUBJECT_TEMPLATE       = 'subjectTemplate';
    case TEMPLATE               = 'template';
    case TLS_HEADER             = 'tlsHeader';

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
