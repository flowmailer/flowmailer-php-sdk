<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * ObjectDescription.
 *
 * Generic resource model with an ID and description.
 */
final class ObjectDescription implements ModelInterface
{
    /**
     * Resource description.
     */
    private ?string $description = null;

    /**
     * Resource ID.
     */
    private ?string $id = null;

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
}
