<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

interface OptionsInterface
{
    public static function getDefaultHeaders(): array;

    public function getAccountId(): string;

    public function setAccountId(string $accountId): OptionsInterface;

    public function getClientId(): string;

    public function setClientId(string $clientId): OptionsInterface;

    public function getClientSecret(): string;

    public function setClientSecret(string $clientSecret): OptionsInterface;

    public function getBaseUrl(): string;

    public function setBaseUrl(string $baseUrl): OptionsInterface;

    public function getAuthBaseUrl(): string;

    public function setAuthBaseUrl(string $authBaseUrl): OptionsInterface;

    public function getOAuthScope(): string;

    public function setOAuthScope(string $oauthScope): OptionsInterface;

    public function getPlugin(string $name): array;
}
