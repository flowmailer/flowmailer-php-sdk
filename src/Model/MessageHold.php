<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * MessageHold.
 *
 * Messages that could not be processed
 */
final class MessageHold implements ModelInterface
{
    /**
     * The time flowmailer was done processing this message.
     */
    private ?\DateTimeInterface $backendDone = null;

    /**
     * MIME message data or text for SMS messages.
     */
    private ?string $data = null;

    /**
     * Only for SMS messages.
     */
    private ?byte $dataCoding = null;

    /**
     * Message error text.
     */
    private ?string $errorText = null;

    private ?\stdClass $extraData = null;

    /**
     * The selected flow.
     */
    private ?ObjectDescription $flow = null;

    /**
     * Message ID.
     */
    private ?string $messageId = null;

    /**
     * Message type: `EMAIL`, `SMS` or `LETTER`.
     */
    private ?string $messageType = null;

    /**
     * Message processing failure reason.
     */
    private ?string $reason = null;

    /**
     * Message recipient address.
     */
    private ?string $recipient = null;

    /**
     * Message sender address.
     */
    private ?string $sender = null;

    /**
     * Source system that submitted this message.
     */
    private ?ObjectDescription $source = null;

    /**
     * Message status.
     */
    private ?string $status = null;

    /**
     * Message submit date.
     */
    private ?\DateTimeInterface $submitted = null;

    /**
     * Transaction ID.
     */
    private ?string $transactionId = null;

    public function setBackendDone(?\DateTimeInterface $backendDone = null): self
    {
        $this->backendDone = $backendDone;

        return $this;
    }

    public function getBackendDone(): ?\DateTimeInterface
    {
        return $this->backendDone;
    }

    public function setData(?string $data = null): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setDataCoding(?byte $dataCoding = null): self
    {
        $this->dataCoding = $dataCoding;

        return $this;
    }

    public function getDataCoding(): ?byte
    {
        return $this->dataCoding;
    }

    public function setErrorText(?string $errorText = null): self
    {
        $this->errorText = $errorText;

        return $this;
    }

    public function getErrorText(): ?string
    {
        return $this->errorText;
    }

    public function setExtraData(?\stdClass $extraData = null): self
    {
        $this->extraData = $extraData;

        return $this;
    }

    public function getExtraData(): ?\stdClass
    {
        return $this->extraData;
    }

    public function setFlow(?ObjectDescription $flow = null): self
    {
        $this->flow = $flow;

        return $this;
    }

    public function getFlow(): ?ObjectDescription
    {
        return $this->flow;
    }

    public function setMessageId(?string $messageId = null): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageType(?string $messageType = null): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setReason(?string $reason = null): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setRecipient(?string $recipient = null): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setSender(?string $sender = null): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSource(?ObjectDescription $source = null): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?ObjectDescription
    {
        return $this->source;
    }

    public function setStatus(?string $status = null): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setSubmitted(?\DateTimeInterface $submitted = null): self
    {
        $this->submitted = $submitted;

        return $this;
    }

    public function getSubmitted(): ?\DateTimeInterface
    {
        return $this->submitted;
    }

    public function setTransactionId(?string $transactionId = null): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
}
