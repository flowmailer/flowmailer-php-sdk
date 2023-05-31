<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * DkimKey.
 */
final class DkimKey implements ModelInterface
{
    private ?string $cnameTarget = null;

    private ?string $domain = null;

    private ?string $publicKey = null;

    private ?string $selector = null;

    public function setCnameTarget(?string $cnameTarget = null): self
    {
        $this->cnameTarget = $cnameTarget;

        return $this;
    }

    public function getCnameTarget(): ?string
    {
        return $this->cnameTarget;
    }

    public function setDomain(?string $domain = null): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setPublicKey(?string $publicKey = null): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setSelector(?string $selector = null): self
    {
        $this->selector = $selector;

        return $this;
    }

    public function getSelector(): ?string
    {
        return $this->selector;
    }
}
