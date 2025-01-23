<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\DnsRecordStatus;

/**
 * DnsRecord.
 *
 * DNS record that should be configured
 */
class DnsRecord implements ModelInterface
{
    /**
     * Error messages for this DNS record.
     *
     *  Only filled when DNS records are validated.
     *
     * @var array<int,string>|null
     */
    private ?array $errorMessages = null;

    /**
     * Record name.
     */
    private ?string $name = null;

    /**
     * Current record status.
     *
     *  Only filled when DNS records are validated.
     *
     *  Possible values: `UNKNOWN`: We had an error while validating this DNS record `ERROR`: DNS record is not correct `WARNING`: DNS record is functional but could be improved `OK`: DNS record is ok
     */
    private string|DnsRecordStatus|null $status = DnsRecordStatus::UNKNOWN;

    /**
     * Record type.
     */
    private ?string $type = null;

    /**
     * Record value description in HTML.
     */
    private ?string $value = null;

    /**
     * Warning messages for this DNS record.
     *
     *  Only filled when DNS records are validated.
     *
     * @var array<int,string>|null
     */
    private ?array $warningMessages = null;

    /**
     * @var array<int,string>|null
     */
    private ?array $warnings = null;

    public function setErrorMessages(?array $errorMessages = null): self
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    public function getErrorMessages(): ?array
    {
        return $this->errorMessages;
    }

    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setStatus(string|DnsRecordStatus|null $status = null): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string|DnsRecordStatus|null
    {
        return $this->status;
    }

    public function setType(?string $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setValue(?string $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setWarningMessages(?array $warningMessages = null): self
    {
        $this->warningMessages = $warningMessages;

        return $this;
    }

    public function getWarningMessages(): ?array
    {
        return $this->warningMessages;
    }

    public function setWarnings(?array $warnings = null): self
    {
        $this->warnings = $warnings;

        return $this;
    }

    public function getWarnings(): ?array
    {
        return $this->warnings;
    }
}
