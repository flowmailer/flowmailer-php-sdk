<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * TlsInfo.
 */
class TlsInfo implements ModelInterface
{
    private ?string $cipher = null;

    private ?string $protocol = null;

    public function setCipher(?string $cipher = null): self
    {
        $this->cipher = $cipher;

        return $this;
    }

    public function getCipher(): ?string
    {
        return $this->cipher;
    }

    public function setProtocol(?string $protocol = null): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }
}
