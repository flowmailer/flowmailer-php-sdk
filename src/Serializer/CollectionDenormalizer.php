<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CollectionDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        $modelName = substr((new \ReflectionClass($type))->getShortName(), 0, -10);

        $base = explode('\\', (new \ReflectionClass($type))->getNamespaceName());
        array_pop($base);

        $collectionType = sprintf('\\%s\\Model\\%s', implode('\\', $base), $modelName);
        if (class_exists($collectionType) === false) {
            $base = explode('\\', __NAMESPACE__);
            array_pop($base);

            $collectionType = sprintf('\\%s\\Model\\%s', implode('\\', $base), $modelName);
        }
        $collectionType .= '[]';

        return new $type($this->denormalizer->denormalize($data, $collectionType, $format, $context));
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, ArrayCollection::class);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*'                    => true,
            ArrayCollection::class => true,
        ];
    }
}
