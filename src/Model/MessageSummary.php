<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * MessageSummary.
 *
 * Message statistics summary
 */
class MessageSummary implements ModelInterface
{
    /**
     * Average delivery time in milliseconds.
     */
    private ?int $averageDeliverTimeMillis = null;

    /**
     * Number of times a link has been clicked.
     */
    private ?int $clicked = null;

    /**
     * Number of messages delivered.
     */
    private ?int $delivered = null;

    /**
     * Number of times a message has been opened.
     */
    private ?int $opened = null;

    /**
     * Number of messages processed.
     */
    private ?int $processed = null;

    /**
     * Number of messages sent.
     */
    private ?int $sent = null;

    /**
     * Number of messages in which a link has been clicked.
     */
    private ?int $uniqueClicked = null;

    /**
     * Number of messages that have been opened.
     */
    private ?int $uniqueOpened = null;

    public function setAverageDeliverTimeMillis(?int $averageDeliverTimeMillis = null): self
    {
        $this->averageDeliverTimeMillis = $averageDeliverTimeMillis;

        return $this;
    }

    public function getAverageDeliverTimeMillis(): ?int
    {
        return $this->averageDeliverTimeMillis;
    }

    public function setClicked(?int $clicked = null): self
    {
        $this->clicked = $clicked;

        return $this;
    }

    public function getClicked(): ?int
    {
        return $this->clicked;
    }

    public function setDelivered(?int $delivered = null): self
    {
        $this->delivered = $delivered;

        return $this;
    }

    public function getDelivered(): ?int
    {
        return $this->delivered;
    }

    public function setOpened(?int $opened = null): self
    {
        $this->opened = $opened;

        return $this;
    }

    public function getOpened(): ?int
    {
        return $this->opened;
    }

    public function setProcessed(?int $processed = null): self
    {
        $this->processed = $processed;

        return $this;
    }

    public function getProcessed(): ?int
    {
        return $this->processed;
    }

    public function setSent(?int $sent = null): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getSent(): ?int
    {
        return $this->sent;
    }

    public function setUniqueClicked(?int $uniqueClicked = null): self
    {
        $this->uniqueClicked = $uniqueClicked;

        return $this;
    }

    public function getUniqueClicked(): ?int
    {
        return $this->uniqueClicked;
    }

    public function setUniqueOpened(?int $uniqueOpened = null): self
    {
        $this->uniqueOpened = $uniqueOpened;

        return $this;
    }

    public function getUniqueOpened(): ?int
    {
        return $this->uniqueOpened;
    }
}
