<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * FlowCondition.
 */
class FlowCondition implements ModelInterface
{
    private ?string $expression = null;

    private ?string $matchType = null;

    private ?string $name = null;

    private ?string $type = null;

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

    public function setMatchType(?string $matchType = null): self
    {
        $this->matchType = $matchType;

        return $this;
    }

    public function getMatchType(): ?string
    {
        return $this->matchType;
    }

    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setType(?string $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
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
