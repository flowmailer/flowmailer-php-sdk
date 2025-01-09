<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Plugin;

use Flowmailer\API\FlowmailerInterface;
use Flowmailer\API\OptionsInterface;
use Http\Client\Common\Plugin;
use Http\Message\Authentication\Bearer;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

class AuthTokenPlugin implements Plugin
{
    private int $retriesLeft = 3;

    private readonly CacheInterface $cache;

    public function __construct(
        private readonly FlowmailerInterface $client,
        private readonly OptionsInterface $options,
        ?CacheInterface $cache = null,
        private readonly int $maxRetries = 3,
    ) {
        $this->retriesLeft = $maxRetries;
        $this->cache       = $cache ?? new Psr16Cache(new ArrayAdapter());
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $request = $this->authenticateRequest($request);

        return $next($request)->then(function (ResponseInterface $response) use ($request, $first) {
            if (401 === $response->getStatusCode() && $this->retriesLeft > 0) {
                --$this->retriesLeft;

                $token     = null;
                $exception = null;
                try {
                    $token = $this->getToken(true);
                } catch (\Throwable $exception) {
                }

                if (is_null($token) && $this->retriesLeft == 0) {
                    throw new \RuntimeException(sprintf('Failed to get a new token after %d retries', $this->maxRetries), 0, $exception);
                }

                $request = (new Bearer($token))->authenticate($request);

                return $first($request)->wait();
            }

            $this->retriesLeft = $this->maxRetries;

            return $response;
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getToken($refresh = false): string
    {
        $cacheKey = 'flowmailer_token_'.$this->options->getAccountId().'_'.$this->options->getClientId();

        if ($refresh === true || is_null($cachedToken = $this->cache->get($cacheKey))) {
            $tokenData = $this->client->createOAuthToken($this->options->getClientId(), $this->options->getClientSecret(), 'client_credentials', $this->options->getOAuthScope());
            $this->cache->set($cacheKey, $tokenData->getAccessToken(), $tokenData->getExpiresIn());

            return $tokenData->getAccessToken();
        }

        return $cachedToken;
    }

    private function authenticateRequest(RequestInterface $request): RequestInterface
    {
        $token   = null;
        $counter = $this->retriesLeft + 1;
        $refresh = false;

        while (
            $request->hasHeader('Authorization') === false
            && count($request->getHeader('Authorization')) === 0
            && $counter > 0
        ) {
            try {
                $exception = null;
                $token     = $this->getToken($refresh);
                $request   = (new Bearer($token))->authenticate($request);
            } catch (\Throwable $exception) {
            }

            $refresh = true;
            --$counter;
        }

        $this->retriesLeft = $counter;

        if (is_null($token) && $this->retriesLeft == 0) {
            throw new \RuntimeException(sprintf('Failed to get a new token after %d retries', $this->maxRetries), 0, $exception);
        }
        if (is_null($exception) === false) {
            throw $exception;
        }

        return $request;
    }
}
