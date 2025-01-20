<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\HeaderCollection;
use Flowmailer\API\Enum\FlowStepExternalDataRequestMethod;

/**
 * FlowStepExternalData.
 */
class FlowStepExternalData implements ModelInterface
{
    /**
     * When true the result variable will be filled with a structure that also contains the response headers. When false the result variable will be filled with just the response body.
     */
    private ?bool $fullResponseInVariable = null;

    /**
     * Template text for the request body.
     *
     *  Only useful for the following request methods `POST`, `PUT` and `PATCH`
     */
    private ?string $requestBodyTemplate = null;

    /**
     * Request headers for external data HTTP request.
     */
    private ?HeaderCollection $requestHeaders = null;

    /**
     * HTTP request method.
     *
     *  Valid values: `GET` `POST` `PUT` `PATCH` `DELETE`
     */
    private string|FlowStepExternalDataRequestMethod|null $requestMethod = null;

    /**
     * Format of the external data, `json` or `json`.
     */
    private ?string $resultFormat = null;

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

    public function setFullResponseInVariable(?bool $fullResponseInVariable = null): self
    {
        $this->fullResponseInVariable = $fullResponseInVariable;

        return $this;
    }

    public function getFullResponseInVariable(): ?bool
    {
        return $this->fullResponseInVariable;
    }

    public function setRequestBodyTemplate(?string $requestBodyTemplate = null): self
    {
        $this->requestBodyTemplate = $requestBodyTemplate;

        return $this;
    }

    public function getRequestBodyTemplate(): ?string
    {
        return $this->requestBodyTemplate;
    }

    public function setRequestHeaders(?HeaderCollection $requestHeaders = null): self
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    public function getRequestHeaders(): ?HeaderCollection
    {
        return $this->requestHeaders;
    }

    public function setRequestMethod(
        string|FlowStepExternalDataRequestMethod|null $requestMethod = null,
    ): self {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestMethod(): string|FlowStepExternalDataRequestMethod|null
    {
        return $this->requestMethod;
    }

    public function setResultFormat(?string $resultFormat = null): self
    {
        $this->resultFormat = $resultFormat;

        return $this;
    }

    public function getResultFormat(): ?string
    {
        return $this->resultFormat;
    }

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
