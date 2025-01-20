<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\SampleCollection;
use Flowmailer\API\Enum\SourceType;

/**
 * Source.
 *
 * Information about a source system
 *
 *  A source system can submit messages to flowmailer.
 */
class Source implements ModelInterface, \Stringable
{
    /**
     * Source description.
     */
    private string $description;
    /**
     * Email DSN messages will be sent to this address.
     */
    private ?string $dsnAddress = null;
    /**
     * Disable sending DSN messages for this source.
     */
    private ?bool $dsnDisable     = null;
    private ?\stdClass $extraData = null;
    /**
     * Email feedback loop messages will be sent to this address.
     */
    private ?string $feedbackLoopAddress = null;
    /**
     * Human readable notifications for undelivered messages will be sent to this address.
     */
    private ?string $humanReadableDsnAddress = null;
    /**
     * Enable sending human readable notifications for undelivered messages for this source.
     */
    private ?bool $humanReadableDsnEnable = null;
    /**
     * Source ID.
     */
    private ?string $id = null;
    /**
     * Date this source was last active.
     */
    private ?\DateTimeInterface $lastActive = null;
    /**
     * Maximum message size in bytes.
     */
    private ?int $maxMessageSize = null;
    /**
     * Message statistics summary for this source.
     */
    private ?MessageSummary $messageSummary = null;
    /**
     * Message statistics for this source.
     */
    private ?SampleCollection $statistics = null;
    private bool $tlsRequired;
    /**
     * Source type: `API`, `SMTP`, `SMTP_RCPT`, `SMTP_DOMAIN` `SMPP`, or `FLOWMAILER`.
     */
    private string|SourceType $type;

    public function __toString(): string
    {
        return (string) $this->id;
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

    public function setDsnAddress(?string $dsnAddress = null): self
    {
        $this->dsnAddress = $dsnAddress;

        return $this;
    }

    public function getDsnAddress(): ?string
    {
        return $this->dsnAddress;
    }

    public function setDsnDisable(?bool $dsnDisable = null): self
    {
        $this->dsnDisable = $dsnDisable;

        return $this;
    }

    public function getDsnDisable(): ?bool
    {
        return $this->dsnDisable;
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

    public function setFeedbackLoopAddress(?string $feedbackLoopAddress = null): self
    {
        $this->feedbackLoopAddress = $feedbackLoopAddress;

        return $this;
    }

    public function getFeedbackLoopAddress(): ?string
    {
        return $this->feedbackLoopAddress;
    }

    public function setHumanReadableDsnAddress(?string $humanReadableDsnAddress = null): self
    {
        $this->humanReadableDsnAddress = $humanReadableDsnAddress;

        return $this;
    }

    public function getHumanReadableDsnAddress(): ?string
    {
        return $this->humanReadableDsnAddress;
    }

    public function setHumanReadableDsnEnable(?bool $humanReadableDsnEnable = null): self
    {
        $this->humanReadableDsnEnable = $humanReadableDsnEnable;

        return $this;
    }

    public function getHumanReadableDsnEnable(): ?bool
    {
        return $this->humanReadableDsnEnable;
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

    public function setLastActive(?\DateTimeInterface $lastActive = null): self
    {
        $this->lastActive = $lastActive;

        return $this;
    }

    public function getLastActive(): ?\DateTimeInterface
    {
        return $this->lastActive;
    }

    public function setMaxMessageSize(?int $maxMessageSize = null): self
    {
        $this->maxMessageSize = $maxMessageSize;

        return $this;
    }

    public function getMaxMessageSize(): ?int
    {
        return $this->maxMessageSize;
    }

    public function setMessageSummary(?MessageSummary $messageSummary = null): self
    {
        $this->messageSummary = $messageSummary;

        return $this;
    }

    public function getMessageSummary(): ?MessageSummary
    {
        return $this->messageSummary;
    }

    public function setStatistics(?SampleCollection $statistics = null): self
    {
        $this->statistics = $statistics;

        return $this;
    }

    public function getStatistics(): ?SampleCollection
    {
        return $this->statistics;
    }

    public function setTlsRequired(bool $tlsRequired): self
    {
        $this->tlsRequired = $tlsRequired;

        return $this;
    }

    public function getTlsRequired(): bool
    {
        return $this->tlsRequired;
    }

    public function setType(string|SourceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string|SourceType
    {
        return $this->type;
    }
}
