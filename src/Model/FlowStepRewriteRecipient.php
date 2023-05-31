<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\MessageType;

/**
 * FlowStepRewriteRecipient.
 */
final class FlowStepRewriteRecipient implements ModelInterface
{
    private string|MessageType|null $messageType = null;

    private ?string $recipientNameTemplate = null;

    private ?string $recipientTemplate = null;

    private ?bool $rewriteHeaders = null;

    public function setMessageType(string|MessageType|null $messageType = null): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType(): string|MessageType|null
    {
        return $this->messageType;
    }

    public function setRecipientNameTemplate(?string $recipientNameTemplate = null): self
    {
        $this->recipientNameTemplate = $recipientNameTemplate;

        return $this;
    }

    public function getRecipientNameTemplate(): ?string
    {
        return $this->recipientNameTemplate;
    }

    public function setRecipientTemplate(?string $recipientTemplate = null): self
    {
        $this->recipientTemplate = $recipientTemplate;

        return $this;
    }

    public function getRecipientTemplate(): ?string
    {
        return $this->recipientTemplate;
    }

    public function setRewriteHeaders(?bool $rewriteHeaders = null): self
    {
        $this->rewriteHeaders = $rewriteHeaders;

        return $this;
    }

    public function getRewriteHeaders(): ?bool
    {
        return $this->rewriteHeaders;
    }
}
