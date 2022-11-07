<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomRequestOptions
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

    public function getBody(): ?string
    {
        return $this->options['body'];
    }

    public function getPath(): array
    {
        return $this->options['path'];
    }

    public function getMatrices(): array
    {
        return $this->options['matrices'];
    }

    public function getQuery(): array
    {
        return $this->options['query'];
    }

    public function getHeaders(): array
    {
        return $this->options['headers'];
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('body', null);
        $resolver->setAllowedTypes('body', ['null', 'string']);

        $resolver->setDefault('path', []);
        $resolver->setAllowedTypes('path', ['array']);

        $resolver->setDefault('matrices', []);
        $resolver->setAllowedTypes('matrices', ['array']);

        $resolver->setDefault('query', []);
        $resolver->setAllowedTypes('query', ['array']);

        $resolver->setDefault('headers', []);
        $resolver->setAllowedTypes('headers', ['array']);
    }
}
