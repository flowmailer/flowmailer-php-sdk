<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\DataExpressionCollection;
use Flowmailer\API\Collection\HeaderCollection;

/**
 * EventFlowRuleSimple.
 *
 * Conditions which must be true for a event to use a flow
 */
class EventFlowRuleSimple implements ModelInterface
{
    /**
     * Data expressions which must be present in the message.
     */
    private ?DataExpressionCollection $dataExpressions = null;

    /**
     * Flow ID of the flow the events will use if the specified conditions are true.
     */
    private ?string $eventFlowId = null;

    /**
     * Match type of the event.
     */
    private ?string $eventType = null;

    /**
     * Email headers which must be present in the message.
     */
    private ?HeaderCollection $headers = null;

    /**
     * Name of the link that was clicked for CLICK events.
     */
    private ?string $linkName = null;

    /**
     * Url of the link that was clicked for CLICK events.
     */
    private ?string $linkTarget = null;

    /**
     * Message Flow ID which handled the original message.
     */
    private ?string $messageFlowId = null;

    public function setDataExpressions(?DataExpressionCollection $dataExpressions = null): self
    {
        $this->dataExpressions = $dataExpressions;

        return $this;
    }

    public function getDataExpressions(): ?DataExpressionCollection
    {
        return $this->dataExpressions;
    }

    public function setEventFlowId(?string $eventFlowId = null): self
    {
        $this->eventFlowId = $eventFlowId;

        return $this;
    }

    public function getEventFlowId(): ?string
    {
        return $this->eventFlowId;
    }

    public function setEventType(?string $eventType = null): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
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

    public function setLinkName(?string $linkName = null): self
    {
        $this->linkName = $linkName;

        return $this;
    }

    public function getLinkName(): ?string
    {
        return $this->linkName;
    }

    public function setLinkTarget(?string $linkTarget = null): self
    {
        $this->linkTarget = $linkTarget;

        return $this;
    }

    public function getLinkTarget(): ?string
    {
        return $this->linkTarget;
    }

    public function setMessageFlowId(?string $messageFlowId = null): self
    {
        $this->messageFlowId = $messageFlowId;

        return $this;
    }

    public function getMessageFlowId(): ?string
    {
        return $this->messageFlowId;
    }
}
