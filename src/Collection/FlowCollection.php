<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Flowmailer\API\Model\Flow;

/**
 * @extends ArrayCollection<int,Flow>
 */
class FlowCollection extends ArrayCollection
{
}
