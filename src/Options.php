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

final class Options
{
    private readonly array $options;

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

    public function getAccountId(): string
    {
        return $this->options['account_id'];
    }

    public function getClientId(): string
    {
        return $this->options['client_id'];
    }

    public function getClientSecret(): string
    {
        return $this->options['client_secret'];
    }

    public function getBaseUrl(): string
    {
        return $this->options['base_url'];
    }

    public function getAuthBaseUrl(): string
    {
        return $this->options['auth_base_url'];
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
                    'header_set' => [
                        'Accept'       => sprintf('application/vnd.flowmailer.%s+json', Flowmailer::API_VERSION),
                        'Content-Type' => sprintf('application/vnd.flowmailer.%s+json', Flowmailer::API_VERSION),
                        'Connection'   => 'Keep-Alive',
                        'Keep-Alive'   => '300',
                        'User-Agent'   => sprintf('FlowMailer PHP SDK %s for API %s', InstalledVersions::getVersion('flowmailer/flowmailer-php-sdk'), Flowmailer::API_VERSION),
                    ],
                    'retry' => [
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
        $resolver->setAllowedValues('protocol', ['http', 'https']);
        $resolver->setAllowedTypes('plugins', 'array');
    }
}
