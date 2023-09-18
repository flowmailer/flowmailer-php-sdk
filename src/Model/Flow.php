<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\FlowStepCollection;
use Flowmailer\API\Collection\SampleCollection;

/**
 * Flow.
 *
 * Message flow
 */
class Flow implements ModelInterface, \Stringable
{
    /**
     * Flow description.
     */
    private string $description;
    /**
     * Flow ID.
     */
    private ?string $id = null;

    private ?MessageSummary $messageSummary = null;

    private ?SampleCollection $statistics = null;
    /**
     * Flow steps that each message in this flow will be processed by.
     */
    private ?FlowStepCollection $steps = null;
    /**
     * Id of the flow template.
     */
    private string $templateId;

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

    public function setId(?string $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function setSteps(?FlowStepCollection $steps = null): self
    {
        $this->steps = $steps;

        return $this;
    }

    public function getSteps(): ?FlowStepCollection
    {
        return $this->steps;
    }

    public function setTemplateId(string $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }
}
