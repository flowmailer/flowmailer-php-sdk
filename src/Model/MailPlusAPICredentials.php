<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * MailPlusAPICredentials.
 *
 * Spotler API credentials
 */
class MailPlusAPICredentials implements ModelInterface
{
    /**
     * Consumer key.
     */
    private string $consumerKey;

    /**
     * Consumer secret.
     */
    private string $consumerSecret;

    public function setConsumerKey(string $consumerKey): self
    {
        $this->consumerKey = $consumerKey;

        return $this;
    }

    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    public function setConsumerSecret(string $consumerSecret): self
    {
        $this->consumerSecret = $consumerSecret;

        return $this;
    }

    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }
}
