<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepDnsLookup.
 */
final class FlowStepDnsLookup implements ModelInterface
{
    private string $domainNameTemplate;

    private ?bool $ignoreErrors = null;

    private string $recordType;

    /**
     * Variable to store the external content in.
     */
    private string $resultVariable;

    public function setDomainNameTemplate(string $domainNameTemplate): self
    {
        $this->domainNameTemplate = $domainNameTemplate;

        return $this;
    }

    public function getDomainNameTemplate(): string
    {
        return $this->domainNameTemplate;
    }

    public function setIgnoreErrors(?bool $ignoreErrors = null): self
    {
        $this->ignoreErrors = $ignoreErrors;

        return $this;
    }

    public function getIgnoreErrors(): ?bool
    {
        return $this->ignoreErrors;
    }

    public function setRecordType(string $recordType): self
    {
        $this->recordType = $recordType;

        return $this;
    }

    public function getRecordType(): string
    {
        return $this->recordType;
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
}
