<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\FlowStepCollection;

/**
 * EventFlow.
 *
 * Event flow
 */
class EventFlow implements ModelInterface
{
    /**
     * Flow description.
     */
    private string $description;

    /**
     * Flow ID.
     */
    private ?string $id = null;

    /**
     * Id of the flow parent.
     */
    private ?string $parentId = null;

    /**
     * Flow steps that each message in this flow will be processed by.
     */
    private ?FlowStepCollection $steps = null;

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

    public function setParentId(?string $parentId = null): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
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
}
