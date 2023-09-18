<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * InboundRecipient.
 */
class InboundRecipient implements ModelInterface
{
    private string $destinationRecipient;

    private ?string $inboundAddress = null;

    public function setDestinationRecipient(string $destinationRecipient): self
    {
        $this->destinationRecipient = $destinationRecipient;

        return $this;
    }

    public function getDestinationRecipient(): string
    {
        return $this->destinationRecipient;
    }

    public function setInboundAddress(?string $inboundAddress = null): self
    {
        $this->inboundAddress = $inboundAddress;

        return $this;
    }

    public function getInboundAddress(): ?string
    {
        return $this->inboundAddress;
    }
}
