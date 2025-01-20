<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\AddressCollection;
use Flowmailer\API\Collection\HeaderCollection;
use Flowmailer\API\Collection\MessageEventCollection;
use Flowmailer\API\Enum\MessageType;

/**
 * Message.
 */
class Message implements ModelInterface
{
    /**
     * The time flowmailer was done processing this message.
     */
    private ?\DateTimeInterface $backendDone = null;

    /**
     * The time flowmailer started processing this message.
     */
    private ?\DateTimeInterface $backendStart = null;

    /**
     * Message events.
     *
     *  Ordered by received, new events first.
     */
    private ?MessageEventCollection $events = null;

    /**
     * Flow this message was processed in.
     */
    private ?ObjectDescription $flow = null;

    /**
     * The email address in ` From` email header.
     */
    private ?string $from = null;

    /**
     * The address in ` From` email header.
     */
    private ?Address $fromAddress = null;

    /**
     * E-Mail headers of the submitted email message.
     *
     *  Only applicable when `messageType` = `EMAIL` and `addheaders` parameter is `true`
     */
    private ?HeaderCollection $headersIn = null;

    /**
     * Headers of the final e-mail.
     *
     *  Only applicable when `messageType` = `EMAIL` and `addheaders` parameter is `true`
     */
    private ?HeaderCollection $headersOut = null;

    /**
     * Message id.
     */
    private ?string $id = null;

    /**
     * Link for the message details page. With resend button.
     */
    private ?string $messageDetailsLink = null;

    /**
     * Content of the `Message-ID` email header.
     */
    private ?string $messageIdHeader = null;

    /**
     * Message type: `EMAIL`, `SMS` or `LETTER`.
     */
    private string|MessageType|null $messageType = null;

    /**
     * Last online link.
     */
    private ?string $onlineLink = null;

    /**
     * Recipient address.
     */
    private ?string $recipientAddress = null;

    /**
     * Sender address.
     */
    private ?string $senderAddress = null;

    /**
     * Source system that submitted this message.
     */
    private ?ObjectDescription $source = null;

    /**
     * Current message status.
     */
    private ?string $status = null;

    /**
     * Message subject.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $subject = null;

    /**
     * The time this message was submitted to flowmailer.
     */
    private ?\DateTimeInterface $submitted = null;

    /**
     * Message tags, only available for api calls with `addtags=true`.
     *
     * @var array<int,string>|null
     */
    private ?array $tags = null;

    /**
     * The recipients in the ` To` email header.
     */
    private ?AddressCollection $toAddressList = null;

    /**
     * The SMTP transaction id, returned with the SMTP ` 250` response.
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

    public function setBackendStart(?\DateTimeInterface $backendStart = null): self
    {
        $this->backendStart = $backendStart;

        return $this;
    }

    public function getBackendStart(): ?\DateTimeInterface
    {
        return $this->backendStart;
    }

    public function setEvents(?MessageEventCollection $events = null): self
    {
        $this->events = $events;

        return $this;
    }

    public function getEvents(): ?MessageEventCollection
    {
        return $this->events;
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

    public function setFrom(?string $from = null): self
    {
        $this->from = $from;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFromAddress(?Address $fromAddress = null): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getFromAddress(): ?Address
    {
        return $this->fromAddress;
    }

    public function setHeadersIn(?HeaderCollection $headersIn = null): self
    {
        $this->headersIn = $headersIn;

        return $this;
    }

    public function getHeadersIn(): ?HeaderCollection
    {
        return $this->headersIn;
    }

    public function setHeadersOut(?HeaderCollection $headersOut = null): self
    {
        $this->headersOut = $headersOut;

        return $this;
    }

    public function getHeadersOut(): ?HeaderCollection
    {
        return $this->headersOut;
    }

    public function setId(?string $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setMessageDetailsLink(?string $messageDetailsLink = null): self
    {
        $this->messageDetailsLink = $messageDetailsLink;

        return $this;
    }

    public function getMessageDetailsLink(): ?string
    {
        return $this->messageDetailsLink;
    }

    public function setMessageIdHeader(?string $messageIdHeader = null): self
    {
        $this->messageIdHeader = $messageIdHeader;

        return $this;
    }

    public function getMessageIdHeader(): ?string
    {
        return $this->messageIdHeader;
    }

    public function setMessageType(string|MessageType|null $messageType = null): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType(): string|MessageType|null
    {
        return $this->messageType;
    }

    public function setOnlineLink(?string $onlineLink = null): self
    {
        $this->onlineLink = $onlineLink;

        return $this;
    }

    public function getOnlineLink(): ?string
    {
        return $this->onlineLink;
    }

    public function setRecipientAddress(?string $recipientAddress = null): self
    {
        $this->recipientAddress = $recipientAddress;

        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setSenderAddress(?string $senderAddress = null): self
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
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

    public function setSubject(?string $subject = null): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
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

    public function setTags(?array $tags = null): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setToAddressList(?AddressCollection $toAddressList = null): self
    {
        $this->toAddressList = $toAddressList;

        return $this;
    }

    public function getToAddressList(): ?AddressCollection
    {
        return $this->toAddressList;
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
