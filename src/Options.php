<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Symfony\Component\OptionsResolver\Options as SymfonyOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Options
{
    /**
     * @var SymfonyOptions
     */
    private $options;

    /**
     * @var OptionsResolver The options resolver
     */
    private $resolver;

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

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('protocol', 'https');
        $resolver->setDefault('host', 'flowmailer.net');
        $resolver->setDefault('base_url', function (SymfonyOptions $options) {
            return sprintf('%s://api.%s', $options['protocol'], $options['host']);
        });
        $resolver->setDefault('auth_base_url', function (SymfonyOptions $options) {
            return sprintf('%s://login.%s', $options['protocol'], $options['host']);
        });
        $resolver->setRequired([
            'account_id',
            'client_secret',
            'client_id',
        ]);

        $resolver->setAllowedTypes('account_id', 'string');
        $resolver->setAllowedTypes('client_id', 'string');
        $resolver->setAllowedTypes('client_secret', 'string');
        $resolver->setAllowedTypes('protocol', 'string');
        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('base_url', 'string');
        $resolver->setAllowedTypes('auth_base_url', 'string');
        $resolver->setAllowedValues('protocol', ['http', 'https']);
    }
}
