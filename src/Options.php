<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Composer\InstalledVersions;
use Symfony\Component\OptionsResolver\Options as SymfonyOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Options implements OptionsInterface
{
    private const PACKAGE_NAME = 'flowmailer/flowmailer-php-sdk';

    private array $options;

    private readonly OptionsResolver $resolver;

    /**
     * @param array<string, mixed> $options The configuration options
     */
    public function __construct(array $options = [])
    {
        $this->resolver = new OptionsResolver();

        $this->configureOptions($this->resolver);

        $this->options = $this->resolver->resolve($options);
    }

    public static function getDefaultHeaders(): array
    {
        return [
            'Accept'       => sprintf('application/vnd.flowmailer.%s+json', Flowmailer::API_VERSION),
            'Content-Type' => sprintf('application/vnd.flowmailer.%s+json', Flowmailer::API_VERSION),
            'Connection'   => 'Keep-Alive',
            'Keep-Alive'   => '300',
            'User-Agent'   => sprintf('FlowMailer PHP SDK %s:%s for API %s', self::PACKAGE_NAME, InstalledVersions::getVersion(self::PACKAGE_NAME), Flowmailer::API_VERSION),
        ];
    }

    public function getAccountId(): string
    {
        return $this->options['account_id'];
    }

    public function setAccountId(string $accountId): OptionsInterface
    {
        $this->options['account_id'] = $accountId;

        return $this;
    }

    public function getClientId(): string
    {
        return $this->options['client_id'];
    }

    public function setClientId(string $clientId): OptionsInterface
    {
        $this->options['client_id'] = $clientId;

        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->options['client_secret'];
    }

    public function setClientSecret(string $clientSecret): OptionsInterface
    {
        $this->options['client_secret'] = $clientSecret;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->options['base_url'];
    }

    public function setBaseUrl(string $baseUrl): OptionsInterface
    {
        $this->options['base_url'] = $baseUrl;

        return $this;
    }

    public function getAuthBaseUrl(): string
    {
        return $this->options['auth_base_url'];
    }

    public function setAuthBaseUrl(string $authBaseUrl): OptionsInterface
    {
        $this->options['auth_base_url'] = $authBaseUrl;

        return $this;
    }

    public function getOAuthScope(): string
    {
        return $this->options['oauth_scope'];
    }

    public function setOAuthScope(string $oauthScope): OptionsInterface
    {
        $this->options['oauth_scope'] = $oauthScope;

        return $this;
    }

    public function getPlugin(string $name): array
    {
        return $this->options['plugins'][$name];
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('protocol', 'https');
        $resolver->setDefault('host', 'flowmailer.net');
        $resolver->setDefault('base_url', fn (SymfonyOptions $options) => sprintf('%s://api.%s', $options['protocol'], $options['host']));
        $resolver->setDefault('auth_base_url', fn (SymfonyOptions $options) => sprintf('%s://login.%s', $options['protocol'], $options['host']));
        $resolver->setDefault('oauth_scope', 'api');

        $resolver->setRequired([
            'account_id',
            'client_secret',
            'client_id',
        ]);

        $resolver->define('plugins')
            ->default([])
            ->allowedTypes('array[]')
            ->allowedValues(static function (array &$elements): bool {
                $defaults = [
                    'error'      => [],
                    'header_set' => self::getDefaultHeaders(),
                    'retry'      => [
                        'retries' => 3,
                    ],
                ];

                $elements = array_merge_recursive($defaults, $elements);

                return true;
            })
        ;

        $resolver->setAllowedTypes('account_id', 'string');
        $resolver->setAllowedTypes('client_id', 'string');
        $resolver->setAllowedTypes('client_secret', 'string');
        $resolver->setAllowedTypes('protocol', 'string');
        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('base_url', 'string');
        $resolver->setAllowedTypes('auth_base_url', 'string');
        $resolver->setAllowedTypes('oauth_scope', 'string');
        $resolver->setAllowedValues('protocol', ['http', 'https']);
        $resolver->setAllowedTypes('plugins', 'array');
    }
}
