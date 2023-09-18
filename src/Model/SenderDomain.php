<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\DnsRecordCollection;
use Flowmailer\API\Collection\ErrorCollection;

/**
 * SenderDomain.
 *
 * a SenderDomain configures which return-path and online tracking domain is used to send messages
 *
 *  DKIM keys are created for each configured SenderDomain.
 *
 *  DNS records are filled for the following api calls:
 *  1. `POST /{account_id}/sender_domains/validate`
 *  2. `GET /{account_id}/sender_domains/{domain_id}`
 *  3. `GET /{account_id}/sender_domains/by_domain/{domain}`
 *
 *  DNS records are validated for the following api calls:
 *  1. `POST /{account_id}/sender_domains/validate`
 *  2. `GET /{account_id}/sender_domains/{domain_id}` when `validate` parameter is `true`
 *  3. `GET /{account_id}/sender_domains/by_domain/{domain}` when `validate` parameter is `true`
 */
class SenderDomain implements ModelInterface
{
    /**
     * List of DNS records that should exist.
     */
    private ?DnsRecordCollection $dnsRecords = null;

    /**
     * ID of this SenderDomain.
     */
    private ?string $id = null;

    /**
     * Domain used for bounce receiving, usually a subdomain of the `senderDomain`.
     */
    private ?string $returnPathDomain = null;

    /**
     * Domain used to select this SenderDomain for emails with a matching `From` header.
     */
    private string $senderDomain;

    /**
     * Only filled when DNS records are validated.
     */
    private ?ErrorCollection $warnings = null;

    /**
     * Domain used for online tracking, usually a subdomain of the `senderDomain`.
     */
    private ?string $webDomain = null;

    public function setDnsRecords(?DnsRecordCollection $dnsRecords = null): self
    {
        $this->dnsRecords = $dnsRecords;

        return $this;
    }

    public function getDnsRecords(): ?DnsRecordCollection
    {
        return $this->dnsRecords;
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

    public function setReturnPathDomain(?string $returnPathDomain = null): self
    {
        $this->returnPathDomain = $returnPathDomain;

        return $this;
    }

    public function getReturnPathDomain(): ?string
    {
        return $this->returnPathDomain;
    }

    public function setSenderDomain(string $senderDomain): self
    {
        $this->senderDomain = $senderDomain;

        return $this;
    }

    public function getSenderDomain(): string
    {
        return $this->senderDomain;
    }

    public function setWarnings(?ErrorCollection $warnings = null): self
    {
        $this->warnings = $warnings;

        return $this;
    }

    public function getWarnings(): ?ErrorCollection
    {
        return $this->warnings;
    }

    public function setWebDomain(?string $webDomain = null): self
    {
        $this->webDomain = $webDomain;

        return $this;
    }

    public function getWebDomain(): ?string
    {
        return $this->webDomain;
    }
}
