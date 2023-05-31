<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\TemplateEngine;
use Flowmailer\API\Enum\TemplateMimeType;

/**
 * Template.
 *
 * A flowmailer content template
 */
final class Template implements ModelInterface, \Stringable
{
    /**
     * Content-ID header (required for disposition `related`).
     *
     *  Example: `<part1.DE1D8F7E.E51807FF@flowmailer.com>`
     *
     *  Only supported for custom content-types.
     */
    private ?string $contentId = null;
    /**
     * Template content.
     */
    private string $data;
    /**
     * Decode Base64.
     *
     *  Only supported for custom content-types.
     */
    private ?bool $decodeBase64 = null;
    /**
     * Template description.
     */
    private string $description;
    /**
     * Content-Disposition header for the attachment.
     *
     *  Supported values include: `attachment`, `inline` and `related`
     *
     *  Special value `related` should be used for images referenced in the HTML.
     *
     *  Only supported for custom content-types.
     */
    private ?string $disposition = null;
    /**
     * Prevents this template from being updated when copying to another account.
     *
     *  This flag is checked on the source template.
     */
    private ?bool $doNotUpdateOnCopy = null;
    /**
     * Content filename.
     *
     *  Only supported for custom content-types and `application/vnd.flowmailer.itext+pdf`.
     */
    private ?string $filename = null;
    /**
     * Template ID.
     */
    private ?string $id = null;
    /**
     * Supported mime types:.
     *
     * - text/plain
     * - text/html
     * - application/vnd.flowmailer.itext+pdf
     */
    private string|TemplateMimeType $mimeType;
    /**
     * The only supported template engine is `freemarker-2.3.20`.
     */
    private string|TemplateEngine $templateEngine = TemplateEngine::FREEMARKER_2_3_20;

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function setContentId(?string $contentId = null): self
    {
        $this->contentId = $contentId;

        return $this;
    }

    public function getContentId(): ?string
    {
        return $this->contentId;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setDecodeBase64(?bool $decodeBase64 = null): self
    {
        $this->decodeBase64 = $decodeBase64;

        return $this;
    }

    public function getDecodeBase64(): ?bool
    {
        return $this->decodeBase64;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDisposition(?string $disposition = null): self
    {
        $this->disposition = $disposition;

        return $this;
    }

    public function getDisposition(): ?string
    {
        return $this->disposition;
    }

    public function setDoNotUpdateOnCopy(?bool $doNotUpdateOnCopy = null): self
    {
        $this->doNotUpdateOnCopy = $doNotUpdateOnCopy;

        return $this;
    }

    public function getDoNotUpdateOnCopy(): ?bool
    {
        return $this->doNotUpdateOnCopy;
    }

    public function setFilename(?string $filename = null): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
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

    public function setMimeType(string|TemplateMimeType $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getMimeType(): string|TemplateMimeType
    {
        return $this->mimeType;
    }

    public function setTemplateEngine(string|TemplateEngine $templateEngine): self
    {
        $this->templateEngine = $templateEngine;

        return $this;
    }

    public function getTemplateEngine(): string|TemplateEngine
    {
        return $this->templateEngine;
    }
}
