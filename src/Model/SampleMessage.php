<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\MessageType;

/**
 * SampleMessage.
 */
class SampleMessage implements ModelInterface
{
    private ?\DateTimeInterface $created = null;

    private string $description;

    private ?array $extraData = null;

    private ?string $fromAddress = null;

    private ?string $fromName = null;

    private ?string $id = null;

    private string|MessageType $messageType;

    private ?string $mimedata = null;

    private ?string $sender = null;

    private ?ObjectDescription $source = null;

    public function setCreated(?\DateTimeInterface $created = null): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setExtraData(?array $extraData = null): self
    {
        $this->extraData = $extraData;

        return $this;
    }

    public function getExtraData(): ?array
    {
        return $this->extraData;
    }

    public function setFromAddress(?string $fromAddress = null): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromName(?string $fromName = null): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
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

    public function setMessageType(string|MessageType $messageType): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType(): string|MessageType
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
}
