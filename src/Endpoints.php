<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Flowmailer\API\Collection\MessageCollection;
use Flowmailer\API\Collection\MessageEventCollection;
use Flowmailer\API\Model\Account;
use Flowmailer\API\Model\AccountUser;
use Flowmailer\API\Model\Message;
use Flowmailer\API\Model\OAuthTokenResponse;
use Flowmailer\API\Model\SubmitMessage;
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

        return $items;
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

        return $items;
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
