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
    public function __construct(
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s,%s', $this->getStartDate()->format(DATE_ISO8601), $this->getEndDate()->format(DATE_ISO8601));
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): DateRange
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): DateRange
    {
        $this->endDate = $endDate;

        return $this;
    }
}
