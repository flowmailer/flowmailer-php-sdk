<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\SampleCollection;

/**
 * DataSet.
 *
 * Data set with statistics
 */
final class DataSet implements ModelInterface
{
    /**
     * Data set name.
     */
    private ?string $name = null;

    /**
     * List of samples in this dataset.
     */
    private ?SampleCollection $samples = null;

    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSamples(?SampleCollection $samples = null): self
    {
        $this->samples = $samples;

        return $this;
    }

    public function getSamples(): ?SampleCollection
    {
        return $this->samples;
    }
}
