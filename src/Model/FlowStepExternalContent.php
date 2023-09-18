<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepExternalContent.
 */
class FlowStepExternalContent implements ModelInterface
{
    /**
     * Variable to store the external content in.
     */
    private ?string $resultVariable = null;

    /**
     * URL to load the external content from.
     *
     *  Template variables can be used in this field.
     */
    private string $urlTemplate;

    public function setResultVariable(?string $resultVariable = null): self
    {
        $this->resultVariable = $resultVariable;

        return $this;
    }

    public function getResultVariable(): ?string
    {
        return $this->resultVariable;
    }

    public function setUrlTemplate(string $urlTemplate): self
    {
        $this->urlTemplate = $urlTemplate;

        return $this;
    }

    public function getUrlTemplate(): string
    {
        return $this->urlTemplate;
    }
}
