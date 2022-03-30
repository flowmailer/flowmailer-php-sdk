<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Logger;

use Http\Client\Common\Plugin\Journal as JournalInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Journal implements JournalInterface
{
    private ?\Psr\Log\LoggerInterface $journalLogger = null;

    public function __construct(LoggerInterface $journalLogger = null)
    {
        $this->journalLogger = $journalLogger ?: new NullLogger();
    }

    public function addSuccess(RequestInterface $request, ResponseInterface $response)
    {
        if ($response->getStatusCode() === 201) {
            $this->journalLogger->info(sprintf('Created: %s', $response->getHeaderLine('location')));
        }
    }

    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception)
    {
    }
}
