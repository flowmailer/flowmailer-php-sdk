<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\MessageType;

/**
 * Filter.
 *
 * Filtered recipient address
 */
class Filter implements ModelInterface
{
    /**
     * Account ID.
     */
    private ?string $accountId = null;

    /**
     * Filtered recipient address.
     */
    private string $address;

    /**
     * Date on which this filter was added.
     */
    private ?\DateTimeInterface $date = null;

    /**
     * Date on which this filter expires.
     */
    private ?\DateTimeInterface $expiresOn = null;

    /**
     * Filter ID.
     */
    private ?string $id = null;

    /**
     * Message event that was the reason for creating this filter.
     */
    private ?MessageReturn $messageReturn = null;

    /**
     * This filter is for message type: `EMAIL` or `SMS`.
     */
    private string|MessageType $messageType;

    /**
     * Filter reason.
     */
    private ?string $reason = null;

    public function setAccountId(?string $accountId = null): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setDate(?\DateTimeInterface $date = null): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setExpiresOn(?\DateTimeInterface $expiresOn = null): self
    {
        $this->expiresOn = $expiresOn;

        return $this;
    }

    public function getExpiresOn(): ?\DateTimeInterface
    {
        return $this->expiresOn;
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

    public function setMessageReturn(?MessageReturn $messageReturn = null): self
    {
        $this->messageReturn = $messageReturn;

        return $this;
    }

    public function getMessageReturn(): ?MessageReturn
    {
        return $this->messageReturn;
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

    public function setReason(?string $reason = null): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
