<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Flowmailer\API\Model\FlowStep;

/**
 * @extends ArrayCollection<int,FlowStep>
 */
class FlowStepCollection extends ArrayCollection
{
}
