<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Flowmailer\API\Utility\SubmitMessageCreatorIterator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

interface FlowmailerInterface extends EndpointsInterface
{
    public static function init(string $accountId, string $clientId, string $clientSecret, array $options = [], ...$additionalArgs): FlowmailerInterface;

    public function request($method, $path, array $parameters = [], ?string $type = null, bool $autoAddAccountId = true);

    public function setAuthClient(?ClientInterface $authClient = null);

    public function getAuthClient(): ClientInterface;

    public function setHttpClient(?ClientInterface $httpClient = null): FlowmailerInterface;

    public function getHttpClient(): ClientInterface;

    public function setLogger(?LoggerInterface $logger = null): FlowmailerInterface;

    public function getLogger(): LoggerInterface;

    public function getRequestFactory(): RequestFactoryInterface;

    public function getStreamFactory(): StreamFactoryInterface;

    public function withAccountId(string $id): FlowmailerInterface;

    public function handleResponse(ResponseInterface $response, $body = null, $method = '');

    public function submitMessages(SubmitMessageCreatorIterator $submitMessages): \Generator;

    public function getResponse(RequestInterface $request, ?ClientInterface $client = null): ResponseInterface;
}
