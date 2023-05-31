<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerFactory
{
    public static function create(): SerializerInterface
    {
        $reflectionExtractor   = new ReflectionExtractor();
        $phpDocExtractor       = new PhpDocExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor([$reflectionExtractor], [$phpDocExtractor, $reflectionExtractor], [$phpDocExtractor], [$reflectionExtractor], [$reflectionExtractor]);
        $normalizers           = [
            new BackedEnumNormalizer(),
            new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP', DateTimeNormalizer::TIMEZONE_KEY => 'Z']),
            new CollectionDenormalizer(),
            new ArrayDenormalizer(),
            new JsonSerializableNormalizer(),
            new ObjectNormalizer(
                null,
                null,
                null,
                $propertyTypeExtractor,
                null,
                null,
                [
                    AbstractObjectNormalizer::SKIP_NULL_VALUES       => true,
                    AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
                ]
            ),
        ];
        $encoders              = [
            new JsonEncoder(),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
