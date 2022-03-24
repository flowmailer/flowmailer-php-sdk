<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

/**
 * OAuthTokenResponse.
 *
 * Response object which contains the requested access token
 */
final class OAuthTokenResponse implements ModelInterface
{
    /**
     * The requested access token.
     */
    private string $accessToken;

    /**
     * The number of seconds this token is valid.
     */
    private int $expiresIn;

    /**
     * Only `api` is supported.
     */
    private ?string $scope = null;

    /**
     * Type of the returned token, only `bearer` is supported.
     */
    private string $tokenType;

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setExpiresIn(int $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function setScope(?string $scope = null): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setTokenType(string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }
}
