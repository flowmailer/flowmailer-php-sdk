<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Composer\InstalledVersions;
use Enqueue\Client\Message;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Consumption\Result;
use Enqueue\SimpleClient\SimpleClient;
use Flowmailer\API\Model\SubmitMessage;
use Flowmailer\API\Utility\SubmitMessageCreatorIterator;
use Interop\Queue\Context;
use Interop\Queue\Message as PsrMessage;
use Psr\Log\LoggerInterface;

class SubmitMessageQueue
{
    public function __construct(
        private readonly FlowmailerInterface $api,
        private readonly SimpleClient $client,
        private readonly string $topic = 'flowmailer_messages',
    ) {
    }

    public static function init(FlowmailerInterface $api, $queueClientConfig, $topic = 'flowmailer_messages', ?LoggerInterface $logger = null)
    {
        if (InstalledVersions::isInstalled('enqueue/simple-client') === false) {
            throw new \Exception('To be able to queue messages, please install enqueue/simple-client and a suitable provider. Please see the Flowmailer docs.');
        }

        $client = new SimpleClient($queueClientConfig, $logger ?? $api->getLogger());

        return new self($api, $client, $topic);
    }

    /**
     * Send an email or sms message.
     */
    public function submitMessage(SubmitMessage $submitMessage, ?string $priority = null, ?int $delay = null)
    {
        $request = $this->api->createRequestForSubmitMessage($submitMessage);
        $message = new Message();
        if (is_null($priority) === false) {
            $message->setPriority($priority);
        }
        if (is_null($delay) === false) {
            $message->setDelay($delay);
        }
        $message->setBody(
            [
                'uri'     => (string) $request->getUri(),
                'method'  => $request->getMethod(),
                'headers' => array_merge_recursive(Options::getDefaultHeaders(), $request->getHeaders()),
                'body'    => (string) $request->getBody(),
            ]
        );

        $this->client->sendEvent($this->topic, $message);
    }

    /**
     * Send email or sms messages.
     *
     * @param \Iterator $submitMessages
     * @param string    $priority       One of the Enqueue\Client\MessagePriority constants
     */
    public function submitMessages(SubmitMessageCreatorIterator $submitMessages, ?string $priority = null, ?int $delay = null)
    {
        foreach ($submitMessages as $submitMessage) {
            $this->submitMessage($submitMessage, $priority, $delay);
        }
    }

    public function consume()
    {
        $this->client->bindTopic($this->topic, function (PsrMessage $psrMessage, Context $context) {
            $messageData = json_decode($psrMessage->getBody(), true);
            $request     = $this->api
                ->getRequestFactory()
                ->createRequest(
                    $messageData['method'],
                    $messageData['uri'])
                ->withBody(
                    $this->api->getStreamFactory()->createStream($messageData['body'])
                );

            foreach ($messageData['headers'] as $headerName => $headerValue) {
                $request = $request->withHeader($headerName, $headerValue);
            }

            $id = $this->api->handleResponse($this->api->getResponse($request), (string) $request->getBody(), $request->getMethod());

            $replyMessage = $context->createMessage(sprintf('Created: %s', $id));

            return Result::reply($replyMessage);
        });

        $this->client->consume();
    }

    public function getQueueConsumer(): QueueConsumerInterface
    {
        return $this->client->getQueueConsumer();
    }
}
