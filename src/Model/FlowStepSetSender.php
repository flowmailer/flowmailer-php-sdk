<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepSetSender.
 */
class FlowStepSetSender implements ModelInterface
{
    private ?string $senderNameTemplate = null;

    private ?bool $senderSetName = null;

    private ?string $senderTemplate = null;

    public function setSenderNameTemplate(?string $senderNameTemplate = null): self
    {
        $this->senderNameTemplate = $senderNameTemplate;

        return $this;
    }

    public function getSenderNameTemplate(): ?string
    {
        return $this->senderNameTemplate;
    }

    public function setSenderSetName(?bool $senderSetName = null): self
    {
        $this->senderSetName = $senderSetName;

        return $this;
    }

    public function getSenderSetName(): ?bool
    {
        return $this->senderSetName;
    }

    public function setSenderTemplate(?string $senderTemplate = null): self
    {
        $this->senderTemplate = $senderTemplate;

        return $this;
    }

    public function getSenderTemplate(): ?string
    {
        return $this->senderTemplate;
    }
}
