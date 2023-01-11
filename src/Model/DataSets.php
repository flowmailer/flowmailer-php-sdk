<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Collection\DataSetCollection;

/**
 * DataSets.
 *
 * Multiple data sets
 */
final class DataSets implements ModelInterface
{
    /**
     * List of data sets.
     */
    private ?DataSetCollection $list = null;

    public function setList(?DataSetCollection $list = null): self
    {
        $this->list = $list;

        return $this;
    }

    public function getList(): ?DataSetCollection
    {
        return $this->list;
    }
}
