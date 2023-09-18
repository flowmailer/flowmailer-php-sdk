<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * SourceConnectionInfo.
 */
class SourceConnectionInfo implements ModelInterface
{
    private ?string $authMethod = null;

    private ?\DateTimeInterface $lastUsed = null;

    private ?string $remoteIp = null;

    private ?TlsInfo $tls = null;

    public function setAuthMethod(?string $authMethod = null): self
    {
        $this->authMethod = $authMethod;

        return $this;
    }

    public function getAuthMethod(): ?string
    {
        return $this->authMethod;
    }

    public function setLastUsed(?\DateTimeInterface $lastUsed = null): self
    {
        $this->lastUsed = $lastUsed;

        return $this;
    }

    public function getLastUsed(): ?\DateTimeInterface
    {
        return $this->lastUsed;
    }

    public function setRemoteIp(?string $remoteIp = null): self
    {
        $this->remoteIp = $remoteIp;

        return $this;
    }

    public function getRemoteIp(): ?string
    {
        return $this->remoteIp;
    }

    public function setTls(?TlsInfo $tls = null): self
    {
        $this->tls = $tls;

        return $this;
    }

    public function getTls(): ?TlsInfo
    {
        return $this->tls;
    }
}
