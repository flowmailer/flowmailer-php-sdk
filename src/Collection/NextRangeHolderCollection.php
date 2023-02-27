<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Flowmailer\API\Parameter\ContentRange;
use Flowmailer\API\Parameter\ReferenceRange;

class NextRangeHolderCollection extends ArrayCollection implements NextRangeHolderCollectionInterface
{
    private ?ReferenceRange $nextRange = null;

    private ?ContentRange $contentRange = null;

    public function getNextRange(): ?ReferenceRange
    {
        return $this->nextRange;
    }

    public function setNextRange(?ReferenceRange $nextRange = null): self
    {
        $this->nextRange = $nextRange;

        return $this;
    }

    public function getContentRange(): ?ContentRange
    {
        return $this->contentRange;
    }

    public function setContentRange(?ContentRange $contentRange = null): NextRangeHolderCollectionInterface
    {
        $this->contentRange = $contentRange;

        return $this;
    }
}
