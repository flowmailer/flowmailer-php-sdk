<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Parameter;

/**
 * Start and end date in ISO8601 format (without milliseconds) separated by a comma. Start date is inclusive and end date is exclusive.
 */
class DateRange implements \Stringable
{
    public function __construct(private \DateTime $startDate, private \DateTime $endDate)
    {
    }

    public function __toString(): string
    {
        return sprintf('%s,%s', rawurlencode($this->getStartDate()->format(DATE_ISO8601)), rawurlencode($this->getEndDate()->format(DATE_ISO8601)));
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): DateRange
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): DateRange
    {
        $this->endDate = $endDate;

        return $this;
    }
}
