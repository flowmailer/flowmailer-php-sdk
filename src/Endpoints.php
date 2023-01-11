<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Flowmailer\API\Collection\AccountUserCollection;
use Flowmailer\API\Collection\BouncedMessageCollection;
use Flowmailer\API\Collection\MessageArchiveCollection;
use Flowmailer\API\Collection\MessageCollection;
use Flowmailer\API\Collection\MessageEventCollection;
use Flowmailer\API\Collection\MessageHoldCollection;
use Flowmailer\API\Model\Account;
use Flowmailer\API\Model\AccountUser;
use Flowmailer\API\Model\DataSets;
use Flowmailer\API\Model\Message;
use Flowmailer\API\Model\MessageArchive;
use Flowmailer\API\Model\MessageHold;
use Flowmailer\API\Model\OAuthTokenResponse;
use Flowmailer\API\Model\ResendMessage;
use Flowmailer\API\Model\SimulateMessage;
use Flowmailer\API\Model\SimulateMessageResult;
use Flowmailer\API\Model\SubmitMessage;
use Flowmailer\API\Parameter\ContentRange;
use Flowmailer\API\Parameter\DateRange;
use Flowmailer\API\Parameter\ItemsRange;
use Flowmailer\API\Parameter\ReferenceRange;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class Endpoints
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(public SerializerInterface $serializer)
    {
    }

    abstract protected function createRequest(
        $method,
        $path,
        $body,
        array $matrices,
        array $query,
        array $headers
    ): RequestInterface;

    abstract protected function createAuthRequest($method, $path, $formData): RequestInterface;

    abstract public function getAuthClient(): ClientInterface;

    abstract public function handleResponse(ResponseInterface $response, $body = null, $method = '');

    abstract public function getResponse(
        RequestInterface $request,
        ClientInterface $client = null
    ): ResponseInterface;

    abstract protected function getOptions(): OptionsInterface;

    /**
     * Create the RequestInterface for createOAuthToken.
     *
     * @param $clientId     The API client id provided by Flowmailer
     * @param $clientSecret The API client secret provided by Flowmailer
     * @param $grantType    must be `client_credentials`
     * @param $scope        Must be absent or `api`
     * @codeCoverageIgnore
     */
    public function createRequestForCreateOAuthToken(
        $clientId,
        $clientSecret,
        $grantType,
        $scope = 'api'
    ): RequestInterface {
        $data = [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => $grantType,
            'scope'         => $scope,
        ];

        return $this->createAuthRequest('POST', '/oauth/token', $data);
    }

    /**
     * This call is used to request an access token using the client id and secret provided by flowmailer.
     *
     * The form parameters must be posted in `application/x-www-form-urlencoded` format. But the response will be in JSON format.
     *
     * @codeCoverageIgnore
     */
    public function createOAuthToken($clientId, $clientSecret, $grantType, $scope = 'api'): OAuthTokenResponse
    {
        $request  = $this->createRequestForCreateOAuthToken($clientId, $clientSecret, $grantType, $scope);
        $response = $this->handleResponse($this->getResponse($request, $this->getAuthClient()), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, OAuthTokenResponse::class, 'json');
    }

    /**
     * Create the RequestInterface for createAccount.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateAccount(Account $account): RequestInterface
    {
        return $this->createRequest('POST', '/accounts', $account, [], [], []);
    }

    /**
     * Create an account.
     *
     * @codeCoverageIgnore
     */
    public function createAccount(Account $account)
    {
        $request  = $this->createRequestForCreateAccount($account);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }

    /**
     * Create the RequestInterface for getMessageEvents.
     *
     * @param ReferenceRange $range          Limits the returned list
     * @param array          $flowIds        Filter results on message flow ID
     * @param array          $sourceIds      Filter results on message source ID
     * @param bool           $addmessagetags Message tags will be included with each event if this parameter is true
     * @param string         $sortorder
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageEvents(
        ReferenceRange $range = null,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?bool $addmessagetags = false,
        ?string $sortorder = null
    ): RequestInterface {
        $matrices = [
            'flow_ids'   => $flowIds,
            'source_ids' => $sourceIds,
        ];
        $query = [
            'addmessagetags' => $addmessagetags,
            'sortorder'      => $sortorder,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/message_events', $this->getOptions()->getAccountId()), null, $matrices, $query, $headers);
    }

    /**
     * List message events.
     *
     *  Ordered by received, new events first.
     *
     * @codeCoverageIgnore
     */
    public function getMessageEvents(
        ReferenceRange $range = null,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?bool $addmessagetags = false,
        ?string $sortorder = null
    ): MessageEventCollection {
        $request  = $this->createRequestForGetMessageEvents($range, $flowIds, $sourceIds, $addmessagetags, $sortorder);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());
        $items    = $this->serializer->deserialize($response, MessageEventCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getMessageHolds.
     *
     * @param ItemsRange $range     Limits the returned list
     * @param DateRange  $daterange Date range the message was submitted in
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageHolds(
        ItemsRange $range = null,
        ?DateRange $daterange = null
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/message_hold', $this->getOptions()->getAccountId()), null, $matrices, [], $headers);
    }

    /**
     * List messages which could not be processed.
     *
     * @codeCoverageIgnore
     */
    public function getMessageHolds(ItemsRange $range = null, ?DateRange $daterange = null): MessageHoldCollection
    {
        $request  = $this->createRequestForGetMessageHolds($range, $daterange);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());
        $items    = $this->serializer->deserialize($response, MessageHoldCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getMessageHold.
     *
     * @param $messageId Message ID
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageHold($messageId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/message_hold/%2$s', $this->getOptions()->getAccountId(), $messageId), null, [], [], []);
    }

    /**
     * Get a held message by its id.
     *
     * @codeCoverageIgnore
     */
    public function getMessageHold($messageId): MessageHold
    {
        $request  = $this->createRequestForGetMessageHold($messageId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, MessageHold::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessages.
     *
     * @param ReferenceRange $range         Limits the returned list
     * @param array          $flowIds       Filter results on flow ID
     * @param bool           $addevents     Whether to add message events
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink
     * @param bool           $addtags
     * @param string         $sortfield     Sort by INSERTED or SUBMITTED (default INSERTED)
     * @param string         $sortorder
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessages(
        ReferenceRange $range = null,
        ?array $flowIds = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?string $sortfield = null,
        ?string $sortorder = null
    ): RequestInterface {
        $matrices = [
            'flow_ids' => $flowIds,
        ];
        $query = [
            'addevents'     => $addevents,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
            'sortfield'     => $sortfield,
            'sortorder'     => $sortorder,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/messages', $this->getOptions()->getAccountId()), null, $matrices, $query, $headers);
    }

    /**
     * List messages.
     *
     *  This API call can be used to retrieve all messages and keep your database up to date (without missing messages due to paging issues). To do this sortfield must be set to INSERTED, and sortorder should be set to ASC.
     *
     * @codeCoverageIgnore
     */
    public function getMessages(
        ReferenceRange $range = null,
        ?array $flowIds = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?string $sortfield = null,
        ?string $sortorder = null
    ): MessageCollection {
        $request  = $this->createRequestForGetMessages($range, $flowIds, $addevents, $addheaders, $addonlinelink, $addtags, $sortfield, $sortorder);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());
        $items    = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for simulateMessage.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForSimulateMessage(SimulateMessage $simulateMessage): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/messages/simulate', $this->getOptions()->getAccountId()), $simulateMessage, [], [], []);
    }

    /**
     * Simulate an email or sms message.
     *
     * @codeCoverageIgnore
     */
    public function simulateMessage(SimulateMessage $simulateMessage): SimulateMessageResult
    {
        $request  = $this->createRequestForSimulateMessage($simulateMessage);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, SimulateMessageResult::class, 'json');
    }

    /**
     * Create the RequestInterface for submitMessage.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForSubmitMessage(SubmitMessage $submitMessage): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/messages/submit', $this->getOptions()->getAccountId()), $submitMessage, [], [], []);
    }

    /**
     * Send an email or sms message.
     *
     * @codeCoverageIgnore
     */
    public function submitMessage(SubmitMessage $submitMessage)
    {
        $request  = $this->createRequestForSubmitMessage($submitMessage);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }

    /**
     * Create the RequestInterface for getMessage.
     *
     * @param      $messageId Message ID
     * @param bool $addtags
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessage($messageId, ?bool $addtags = false): RequestInterface
    {
        $query = [
            'addtags' => $addtags,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/messages/%2$s', $this->getOptions()->getAccountId(), $messageId), null, [], $query, []);
    }

    /**
     * Get message by id.
     *
     * @codeCoverageIgnore
     */
    public function getMessage($messageId, ?bool $addtags = false): Message
    {
        $request  = $this->createRequestForGetMessage($messageId, $addtags);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, Message::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageArchive.
     *
     * @param      $messageId      Message ID
     * @param bool $addattachments
     * @param bool $adddata
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): RequestInterface {
        $query = [
            'addattachments' => $addattachments,
            'adddata'        => $adddata,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/messages/%2$s/archive', $this->getOptions()->getAccountId(), $messageId), null, [], $query, []);
    }

    /**
     * List the message as archived by one or more flow steps.
     *
     * @codeCoverageIgnore
     */
    public function getMessageArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): MessageArchiveCollection {
        $request  = $this->createRequestForGetMessageArchive($messageId, $addattachments, $adddata);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, MessageArchiveCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageErrorArchive.
     *
     * @param      $messageId
     * @param bool $addattachments
     * @param bool $adddata
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageErrorArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): RequestInterface {
        $query = [
            'addattachments' => $addattachments,
            'adddata'        => $adddata,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/messages/%2$s/error_archive', $this->getOptions()->getAccountId(), $messageId), null, [], $query, []);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMessageErrorArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): MessageArchive {
        $request  = $this->createRequestForGetMessageErrorArchive($messageId, $addattachments, $adddata);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, MessageArchive::class, 'json');
    }

    /**
     * Create the RequestInterface for resendMessage.
     *
     * @param $messageId Message ID
     * @codeCoverageIgnore
     */
    public function createRequestForResendMessage($messageId, ResendMessage $resendMessage): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/messages/%2$s/resend', $this->getOptions()->getAccountId(), $messageId), $resendMessage, [], [], []);
    }

    /**
     * Resend message by id.
     *
     * @codeCoverageIgnore
     */
    public function resendMessage($messageId, ResendMessage $resendMessage)
    {
        $request  = $this->createRequestForResendMessage($messageId, $resendMessage);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }

    /**
     * Create the RequestInterface for getMessageStats.
     *
     * @param DateRange $daterange Date range the messages were submitted in
     * @param array     $flowIds
     * @param int       $interval  Time difference between samples
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageStats(
        DateRange $daterange = null,
        ?array $flowIds = null,
        ?int $interval = null
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
            'flow_ids'  => $flowIds,
        ];
        $query = [
            'interval' => $interval,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/messagestats', $this->getOptions()->getAccountId()), null, $matrices, $query, []);
    }

    /**
     * Get time based message statistics for whole account.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     *
     * @codeCoverageIgnore
     */
    public function getMessageStats(
        DateRange $daterange = null,
        ?array $flowIds = null,
        ?int $interval = null
    ): DataSets {
        $request  = $this->createRequestForGetMessageStats($daterange, $flowIds, $interval);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getUndeliveredMessages.
     *
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the message was submitted in
     * @param DateRange      $receivedrange Date range the message bounced
     * @param bool           $addevents     Whether to add message events
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink
     * @param bool           $addtags
     * @param string         $sortorder
     * @codeCoverageIgnore
     */
    public function createRequestForGetUndeliveredMessages(
        ReferenceRange $range = null,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?string $sortorder = null
    ): RequestInterface {
        $matrices = [
            'daterange'     => $daterange,
            'receivedrange' => $receivedrange,
        ];
        $query = [
            'addevents'     => $addevents,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
            'sortorder'     => $sortorder,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/undeliveredmessages', $this->getOptions()->getAccountId()), null, $matrices, $query, $headers);
    }

    /**
     * List undeliverable messages.
     *
     * @codeCoverageIgnore
     */
    public function getUndeliveredMessages(
        ReferenceRange $range = null,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?string $sortorder = null
    ): BouncedMessageCollection {
        $request  = $this->createRequestForGetUndeliveredMessages($range, $daterange, $receivedrange, $addevents, $addheaders, $addonlinelink, $addtags, $sortorder);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());
        $items    = $this->serializer->deserialize($response, BouncedMessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getUsers.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetUsers(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/users', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getUsers(): AccountUserCollection
    {
        $request  = $this->createRequestForGetUsers();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, AccountUserCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for addUser.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForAddUser(AccountUser $accountUser): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/users', $this->getOptions()->getAccountId()), $accountUser, [], [], []);
    }

    /**
     * Create a user.
     *
     * @codeCoverageIgnore
     */
    public function addUser(AccountUser $accountUser)
    {
        $request  = $this->createRequestForAddUser($accountUser);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }
}
