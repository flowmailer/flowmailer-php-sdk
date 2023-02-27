<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\AttachmentCollection;
use Flowmailer\API\Collection\HeaderCollection;

/**
 * SimulateMessage.
 */
final class SimulateMessage implements ModelInterface
{
    /**
     * Attachments.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?AttachmentCollection $attachments = null;

    /**
     * Extra data that will be available in templates.
     */
    private ?\stdClass $data = null;

    /**
     * `NONE`, `FAILURE` or `DELIVERY_AND_FAILURE`.
     *
     *  Defaults to `NONE` for `SMS`
     *
     *  Only applicable when `messageType` = `SMS`
     */
    private ?string $deliveryNotificationType = null;

    /**
     * Freely configurable value that can be used to select a flow or one of its variants.
     *
     *  Examples: invoice, previsit, ticket.
     */
    private ?string $flowSelector = null;

    /**
     * From header address.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $headerFromAddress = null;

    /**
     * From header name.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $headerFromName = null;

    /**
     * To header address.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $headerToAddress = null;

    /**
     * To header name.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $headerToName = null;

    /**
     * Email headers.
     */
    private ?HeaderCollection $headers = null;

    /**
     * Email HTML content.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $html = null;

    /**
     * `EMAIL`, `SMS` or `LETTER`.
     */
    private string $messageType;

    /**
     * Complete email MIME message with headers.
     *
     *  Only applicable when `messageType` = `EMAIL`
     */
    private ?string $mimedata = null;

    /**
     * Recipient email address or phone number.
     *
     *  For email messages this cannot contain a display name.
     */
    private string $recipientAddress;

    private ?\DateTimeInterface $scheduleAt = null;

    /**
     * Sender email address or phone number.
     *
     *  For email messages this cannot contain a display name.
     */
    private ?string $senderAddress = null;

    private ?string $sourceId = null;

    /**
     * Email subject.
     */
    private ?string $subject = null;

    /**
     * Tags.
     *
     * @var string[]|null
     */
    private ?array $tags = null;

    /**
     * Text content.
     */
    private ?string $text = null;

    public function setAttachments(?AttachmentCollection $attachments = null): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getAttachments(): ?AttachmentCollection
    {
        return $this->attachments;
    }

    public function setData(?\stdClass $data = null): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?\stdClass
    {
        return $this->data;
    }

    public function setDeliveryNotificationType(?string $deliveryNotificationType = null): self
    {
        $this->deliveryNotificationType = $deliveryNotificationType;

        return $this;
    }

    public function getDeliveryNotificationType(): ?string
    {
        return $this->deliveryNotificationType;
    }

    public function setFlowSelector(?string $flowSelector = null): self
    {
        $this->flowSelector = $flowSelector;

        return $this;
    }

    public function getFlowSelector(): ?string
    {
        return $this->flowSelector;
    }

    public function setHeaderFromAddress(?string $headerFromAddress = null): self
    {
        $this->headerFromAddress = $headerFromAddress;

        return $this;
    }

    public function getHeaderFromAddress(): ?string
    {
        return $this->headerFromAddress;
    }

    public function setHeaderFromName(?string $headerFromName = null): self
    {
        $this->headerFromName = $headerFromName;

        return $this;
    }

    public function getHeaderFromName(): ?string
    {
        return $this->headerFromName;
    }

    public function setHeaderToAddress(?string $headerToAddress = null): self
    {
        $this->headerToAddress = $headerToAddress;

        return $this;
    }

    public function getHeaderToAddress(): ?string
    {
        return $this->headerToAddress;
    }

    public function setHeaderToName(?string $headerToName = null): self
    {
        $this->headerToName = $headerToName;

        return $this;
    }

    public function getHeaderToName(): ?string
    {
        return $this->headerToName;
    }

    public function setHeaders(?HeaderCollection $headers = null): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders(): ?HeaderCollection
    {
        return $this->headers;
    }

    public function setHtml(?string $html = null): self
    {
        $this->html = $html;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setMessageType(string $messageType): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function setMimedata(?string $mimedata = null): self
    {
        $this->mimedata = $mimedata;

        return $this;
    }

    public function getMimedata(): ?string
    {
        return $this->mimedata;
    }

    public function setRecipientAddress(string $recipientAddress): self
    {
        $this->recipientAddress = $recipientAddress;

        return $this;
    }

    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
    }

    public function setScheduleAt(?\DateTimeInterface $scheduleAt = null): self
    {
        $this->scheduleAt = $scheduleAt;

        return $this;
    }

    public function getScheduleAt(): ?\DateTimeInterface
    {
        return $this->scheduleAt;
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

    public function setSourceId(?string $sourceId = null): self
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
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

    public function setTags(?array $tags = null): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setText(?string $text = null): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
}
