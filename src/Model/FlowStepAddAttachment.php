<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowStepAddAttachment.
 */
final class FlowStepAddAttachment implements ModelInterface
{
    /**
     * URL to load the external content from.
     *
     *  Template variables can be used in this field.
     */
    private string $urlTemplate;

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
