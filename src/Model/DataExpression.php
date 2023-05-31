<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * DataExpression.
 */
final class DataExpression implements ModelInterface
{
    /**
     * Expression.
     */
    private ?string $expression = null;

    /**
     * Value which must match the result of the expression.
     */
    private ?string $value = null;

    public function setExpression(?string $expression = null): self
    {
        $this->expression = $expression;

        return $this;
    }

    public function getExpression(): ?string
    {
        return $this->expression;
    }

    public function setValue(?string $value = null): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
