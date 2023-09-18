<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\InboundRecipientCollection;
use Flowmailer\API\Enum\Protocol;

/**
 * Credentials.
 *
 * Source system credentials
 */
class Credentials implements ModelInterface, \Stringable
{
    /**
     * Allowed IP addresses that can connect to Flowmailer using these credentials.
     *
     * @var string[]|null
     */
    private ?array $allowedAddresses = null;
    /**
     * @var string[]|null
     */
    private ?array $allowedSenders = null;

    private ?string $authSource = null;

    private ?string $clientId = null;

    private ?string $clientSecret = null;
    /**
     * Contact info for these credentials.
     */
    private ?string $contactInfo = null;

    private ?string $description = null;
    /**
     * Source credentials ID.
     */
    private ?string $id = null;

    private ?string $inboundDomain = null;

    private ?InboundRecipientCollection $inboundRecipients = null;
    /**
     * Password.
     */
    private ?string $password = null;
    /**
     * Protocol: `SMTP`, `SMTP_RCPT`, `SMTP_HEADER`, `SMTP_IPONLY`, or `SMPP`.
     */
    private string|Protocol $protocol;
    /**
     * Valid roles can be `FEATURE_SUBMITMESSAGE` for message submit only, or one of the roles returned by `GET /{account_id}/roles`.
     *
     *  Roles must contain exactly 1 role.
     */
    private ?array $roles = null;

    private ?string $sourceId = null;
    /**
     * Username.
     */
    private ?string $username = null;

    public function __toString(): string
    {
        return (string) $this->clientId;
    }

    public function setAllowedAddresses(?array $allowedAddresses = null): self
    {
        $this->allowedAddresses = $allowedAddresses;

        return $this;
    }

    public function getAllowedAddresses(): ?array
    {
        return $this->allowedAddresses;
    }

    public function setAllowedSenders(?array $allowedSenders = null): self
    {
        $this->allowedSenders = $allowedSenders;

        return $this;
    }

    public function getAllowedSenders(): ?array
    {
        return $this->allowedSenders;
    }

    public function setAuthSource(?string $authSource = null): self
    {
        $this->authSource = $authSource;

        return $this;
    }

    public function getAuthSource(): ?string
    {
        return $this->authSource;
    }

    public function setClientId(?string $clientId = null): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientSecret(?string $clientSecret = null): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setContactInfo(?string $contactInfo = null): self
    {
        $this->contactInfo = $contactInfo;

        return $this;
    }

    public function getContactInfo(): ?string
    {
        return $this->contactInfo;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
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

    public function setInboundDomain(?string $inboundDomain = null): self
    {
        $this->inboundDomain = $inboundDomain;

        return $this;
    }

    public function getInboundDomain(): ?string
    {
        return $this->inboundDomain;
    }

    public function setInboundRecipients(?InboundRecipientCollection $inboundRecipients = null): self
    {
        $this->inboundRecipients = $inboundRecipients;

        return $this;
    }

    public function getInboundRecipients(): ?InboundRecipientCollection
    {
        return $this->inboundRecipients;
    }

    public function setPassword(?string $password = null): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setProtocol(string|Protocol $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getProtocol(): string|Protocol
    {
        return $this->protocol;
    }

    public function setRoles(?array $roles = null): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setSourceId(?string $sourceId = null): self
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setUsername(?string $username = null): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
