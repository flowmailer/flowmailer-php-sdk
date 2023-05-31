<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepLdapSearch.
 */
final class FlowStepLdapSearch implements ModelInterface
{
    /**
     * @var string[]|null
     */
    private ?array $attributes = null;

    private string $baseDNTemplate;

    private ?string $bindDN = null;

    private ?string $bindPassword = null;

    private ?string $filterTemplate = null;

    private string $host;

    private ?int $port = null;

    /**
     * Variable to store the external content in.
     */
    private string $resultVariable;

    /**
     * Search scope.
     *
     *  Valid values: `ONE` `BASE` `SUB`
     */
    private string $searchScope;

    /**
     * Connection TLS Mode.
     *
     *  Valid values: `PLAIN` `TLS` `STARTTLS`
     */
    private string $tlsMode;

    public function setAttributes(?array $attributes = null): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setBaseDNTemplate(string $baseDNTemplate): self
    {
        $this->baseDNTemplate = $baseDNTemplate;

        return $this;
    }

    public function getBaseDNTemplate(): string
    {
        return $this->baseDNTemplate;
    }

    public function setBindDN(?string $bindDN = null): self
    {
        $this->bindDN = $bindDN;

        return $this;
    }

    public function getBindDN(): ?string
    {
        return $this->bindDN;
    }

    public function setBindPassword(?string $bindPassword = null): self
    {
        $this->bindPassword = $bindPassword;

        return $this;
    }

    public function getBindPassword(): ?string
    {
        return $this->bindPassword;
    }

    public function setFilterTemplate(?string $filterTemplate = null): self
    {
        $this->filterTemplate = $filterTemplate;

        return $this;
    }

    public function getFilterTemplate(): ?string
    {
        return $this->filterTemplate;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setPort(?int $port = null): self
    {
        $this->port = $port;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setResultVariable(string $resultVariable): self
    {
        $this->resultVariable = $resultVariable;

        return $this;
    }

    public function getResultVariable(): string
    {
        return $this->resultVariable;
    }

    public function setSearchScope(string $searchScope): self
    {
        $this->searchScope = $searchScope;

        return $this;
    }

    public function getSearchScope(): string
    {
        return $this->searchScope;
    }

    public function setTlsMode(string $tlsMode): self
    {
        $this->tlsMode = $tlsMode;

        return $this;
    }

    public function getTlsMode(): string
    {
        return $this->tlsMode;
    }
}
