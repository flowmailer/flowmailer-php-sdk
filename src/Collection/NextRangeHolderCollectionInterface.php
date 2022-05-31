<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Collection;

use Flowmailer\API\Parameter\ReferenceRange;

interface NextRangeHolderCollectionInterface
{
    public function getNextRange(): ?ReferenceRange;

    public function setNextRange(?ReferenceRange $nextRange = null): self;
}
