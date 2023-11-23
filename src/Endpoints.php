<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Flowmailer\API\Collection\AccountUserCollection;
use Flowmailer\API\Collection\BouncedMessageCollection;
use Flowmailer\API\Collection\CredentialsCollection;
use Flowmailer\API\Collection\EventFlowCollection;
use Flowmailer\API\Collection\FilterCollection;
use Flowmailer\API\Collection\FlowCollection;
use Flowmailer\API\Collection\FlowRuleHierarchyItemCollection;
use Flowmailer\API\Collection\FlowRuleItemCollection;
use Flowmailer\API\Collection\FlowTemplateCollection;
use Flowmailer\API\Collection\MessageArchiveCollection;
use Flowmailer\API\Collection\MessageCollection;
use Flowmailer\API\Collection\MessageEventCollection;
use Flowmailer\API\Collection\MessageHoldCollection;
use Flowmailer\API\Collection\RoleCollection;
use Flowmailer\API\Collection\SenderDomainCollection;
use Flowmailer\API\Collection\SourceCollection;
use Flowmailer\API\Collection\TemplateCollection;
use Flowmailer\API\Model\Account;
use Flowmailer\API\Model\AccountUser;
use Flowmailer\API\Model\Attachment;
use Flowmailer\API\Model\Credentials;
use Flowmailer\API\Model\DataSets;
use Flowmailer\API\Model\EventFlow;
use Flowmailer\API\Model\EventFlowRuleSimple;
use Flowmailer\API\Model\Flow;
use Flowmailer\API\Model\FlowRuleSimple;
use Flowmailer\API\Model\Message;
use Flowmailer\API\Model\MessageArchive;
use Flowmailer\API\Model\MessageHold;
use Flowmailer\API\Model\OAuthTokenResponse;
use Flowmailer\API\Model\Recipient;
use Flowmailer\API\Model\ResendMessage;
use Flowmailer\API\Model\SenderDomain;
use Flowmailer\API\Model\SimulateMessage;
use Flowmailer\API\Model\SimulateMessageResult;
use Flowmailer\API\Model\Source;
use Flowmailer\API\Model\SubmitMessage;
use Flowmailer\API\Model\Template;
use Flowmailer\API\Parameter\ContentRange;
use Flowmailer\API\Parameter\DateRange;
use Flowmailer\API\Parameter\ItemsRange;
use Flowmailer\API\Parameter\ReferenceRange;
use Flowmailer\API\Serializer\ResponseData;
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
        ?ClientInterface $client = null
    ): ResponseInterface;

    abstract protected function getOptions(): OptionsInterface;

    /**
     * Create the RequestInterface for createOAuthToken.
     *
     * @param $clientId     The API client id provided by Flowmailer
     * @param $clientSecret The API client secret provided by Flowmailer
     * @param $grantType    must be `client_credentials`
     * @param $scope        Must be absent or `api`
     *
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
        $request = $this->createRequestForCreateOAuthToken($clientId, $clientSecret, $grantType, $scope);

        return $this->doRequestForCreateOAuthToken($request);
    }

    /**
     * Do the request for createOAuthToken.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateOAuthToken(RequestInterface $request): OAuthTokenResponse
    {
        $responseData = $this->handleResponse($this->getResponse($request, $this->getAuthClient()), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForCreateOAuthToken($responseData);
    }

    /**
     * Deserialize the responseData for createOAuthToken.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForCreateOAuthToken(ResponseData $response): OAuthTokenResponse
    {
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
        $request = $this->createRequestForCreateAccount($account);

        return $this->doRequestForCreateAccount($request);
    }

    /**
     * Do the request for createAccount.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateAccount(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetApiCredentials(?int $sourceId = null): RequestInterface
    {
        $matrices = [
            'source_id' => $sourceId,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/api_credentials', $this->getOptions()->getAccountId()), null, $matrices, [], []);
    }

    /**
     * Get api credentials.
     *
     * @codeCoverageIgnore
     */
    public function getApiCredentials(?int $sourceId = null): CredentialsCollection
    {
        $request = $this->createRequestForGetApiCredentials($sourceId);

        return $this->doRequestForGetApiCredentials($request);
    }

    /**
     * Do the request for getApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetApiCredentials(RequestInterface $request): CredentialsCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetApiCredentials($responseData);
    }

    /**
     * Deserialize the responseData for getApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetApiCredentials(ResponseData $response): CredentialsCollection
    {
        return $this->serializer->deserialize($response, CredentialsCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateApiCredentials(Credentials $credentials): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/api_credentials', $this->getOptions()->getAccountId()), $credentials, [], [], []);
    }

    /**
     * Create api credentials.
     *
     * @codeCoverageIgnore
     */
    public function createApiCredentials(Credentials $credentials): Credentials
    {
        $request = $this->createRequestForCreateApiCredentials($credentials);

        return $this->doRequestForCreateApiCredentials($request);
    }

    /**
     * Do the request for createApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateApiCredentials(RequestInterface $request): Credentials
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForCreateApiCredentials($responseData);
    }

    /**
     * Deserialize the responseData for createApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForCreateApiCredentials(ResponseData $response): Credentials
    {
        return $this->serializer->deserialize($response, Credentials::class, 'json');
    }

    /**
     * Create the RequestInterface for deleteClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteClientApiCredentials($clientId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/api_credentials/%2$s', $this->getOptions()->getAccountId(), $clientId), null, [], [], []);
    }

    /**
     * Delete client api credentials.
     *
     * @codeCoverageIgnore
     */
    public function deleteClientApiCredentials($clientId)
    {
        $request = $this->createRequestForDeleteClientApiCredentials($clientId);

        return $this->doRequestForDeleteClientApiCredentials($request);
    }

    /**
     * Do the request for deleteClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteClientApiCredentials(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetClientApiCredentials($clientId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/api_credentials/%2$s', $this->getOptions()->getAccountId(), $clientId), null, [], [], []);
    }

    /**
     * Get client api credentials.
     *
     * @codeCoverageIgnore
     */
    public function getClientApiCredentials($clientId): Credentials
    {
        $request = $this->createRequestForGetClientApiCredentials($clientId);

        return $this->doRequestForGetClientApiCredentials($request);
    }

    /**
     * Do the request for getClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetClientApiCredentials(RequestInterface $request): Credentials
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetClientApiCredentials($responseData);
    }

    /**
     * Deserialize the responseData for getClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetClientApiCredentials(ResponseData $response): Credentials
    {
        return $this->serializer->deserialize($response, Credentials::class, 'json');
    }

    /**
     * Create the RequestInterface for updateClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateClientApiCredentials($clientId, Credentials $credentials): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/api_credentials/%2$s', $this->getOptions()->getAccountId(), $clientId), $credentials, [], [], []);
    }

    /**
     * Update client api credentials.
     *
     * @codeCoverageIgnore
     */
    public function updateClientApiCredentials($clientId, Credentials $credentials)
    {
        $request = $this->createRequestForUpdateClientApiCredentials($clientId, $credentials);

        return $this->doRequestForUpdateClientApiCredentials($request);
    }

    /**
     * Do the request for updateClientApiCredentials.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateClientApiCredentials(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getEventFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetEventFlowRules(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/event_flow_rules', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * Get flow rule list for all event flows.
     *
     * @codeCoverageIgnore
     */
    public function getEventFlowRules(): FlowRuleItemCollection
    {
        $request = $this->createRequestForGetEventFlowRules();

        return $this->doRequestForGetEventFlowRules($request);
    }

    /**
     * Do the request for getEventFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetEventFlowRules(RequestInterface $request): FlowRuleItemCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetEventFlowRules($responseData);
    }

    /**
     * Deserialize the responseData for getEventFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetEventFlowRules(ResponseData $response): FlowRuleItemCollection
    {
        return $this->serializer->deserialize($response, FlowRuleItemCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getEventFlowRulesHierarchy.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetEventFlowRulesHierarchy(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/event_flow_rules/hierarchy', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * Get flow rule list for all event flows.
     *
     * @codeCoverageIgnore
     */
    public function getEventFlowRulesHierarchy(): FlowRuleHierarchyItemCollection
    {
        $request = $this->createRequestForGetEventFlowRulesHierarchy();

        return $this->doRequestForGetEventFlowRulesHierarchy($request);
    }

    /**
     * Do the request for getEventFlowRulesHierarchy.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetEventFlowRulesHierarchy(RequestInterface $request): FlowRuleHierarchyItemCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetEventFlowRulesHierarchy($responseData);
    }

    /**
     * Deserialize the responseData for getEventFlowRulesHierarchy.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetEventFlowRulesHierarchy(ResponseData $response): FlowRuleHierarchyItemCollection
    {
        return $this->serializer->deserialize($response, FlowRuleHierarchyItemCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getEventFlows.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetEventFlows(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/event_flows', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * List flows per account.
     *
     * @codeCoverageIgnore
     */
    public function getEventFlows(): EventFlowCollection
    {
        $request = $this->createRequestForGetEventFlows();

        return $this->doRequestForGetEventFlows($request);
    }

    /**
     * Do the request for getEventFlows.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetEventFlows(RequestInterface $request): EventFlowCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetEventFlows($responseData);
    }

    /**
     * Deserialize the responseData for getEventFlows.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetEventFlows(ResponseData $response): EventFlowCollection
    {
        return $this->serializer->deserialize($response, EventFlowCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createEventFlow.
     *
     * @param EventFlow $eventFlow Flow object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateEventFlow(EventFlow $eventFlow): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/event_flows', $this->getOptions()->getAccountId()), $eventFlow, [], [], []);
    }

    /**
     * Create a new flow.
     *
     * @codeCoverageIgnore
     */
    public function createEventFlow(EventFlow $eventFlow)
    {
        $request = $this->createRequestForCreateEventFlow($eventFlow);

        return $this->doRequestForCreateEventFlow($request);
    }

    /**
     * Do the request for createEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateEventFlow(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for deleteEventFlow.
     *
     * @param $eventFlowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteEventFlow($eventFlowId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/event_flows/%2$s', $this->getOptions()->getAccountId(), $eventFlowId), null, [], [], []);
    }

    /**
     * Delete flow by id.
     *
     * @codeCoverageIgnore
     */
    public function deleteEventFlow($eventFlowId)
    {
        $request = $this->createRequestForDeleteEventFlow($eventFlowId);

        return $this->doRequestForDeleteEventFlow($request);
    }

    /**
     * Do the request for deleteEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteEventFlow(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getEventFlow.
     *
     * @param $eventFlowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetEventFlow($eventFlowId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/event_flows/%2$s', $this->getOptions()->getAccountId(), $eventFlowId), null, [], [], []);
    }

    /**
     * Get flow by id.
     *
     * @codeCoverageIgnore
     */
    public function getEventFlow($eventFlowId): EventFlow
    {
        $request = $this->createRequestForGetEventFlow($eventFlowId);

        return $this->doRequestForGetEventFlow($request);
    }

    /**
     * Do the request for getEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetEventFlow(RequestInterface $request): EventFlow
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetEventFlow($responseData);
    }

    /**
     * Deserialize the responseData for getEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetEventFlow(ResponseData $response): EventFlow
    {
        return $this->serializer->deserialize($response, EventFlow::class, 'json');
    }

    /**
     * Create the RequestInterface for updateEventFlow.
     *
     * @param           $eventFlowId Flow ID
     * @param EventFlow $eventFlow   Flow object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateEventFlow($eventFlowId, EventFlow $eventFlow): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/event_flows/%2$s', $this->getOptions()->getAccountId(), $eventFlowId), $eventFlow, [], [], []);
    }

    /**
     * Save flow.
     *
     * @codeCoverageIgnore
     */
    public function updateEventFlow($eventFlowId, EventFlow $eventFlow): EventFlow
    {
        $request = $this->createRequestForUpdateEventFlow($eventFlowId, $eventFlow);

        return $this->doRequestForUpdateEventFlow($request);
    }

    /**
     * Do the request for updateEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateEventFlow(RequestInterface $request): EventFlow
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForUpdateEventFlow($responseData);
    }

    /**
     * Deserialize the responseData for updateEventFlow.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForUpdateEventFlow(ResponseData $response): EventFlow
    {
        return $this->serializer->deserialize($response, EventFlow::class, 'json');
    }

    /**
     * Create the RequestInterface for getEventFlowRule.
     *
     * @param $eventFlowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetEventFlowRule($eventFlowId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/event_flows/%2$s/rule', $this->getOptions()->getAccountId(), $eventFlowId), null, [], [], []);
    }

    /**
     * Get flow conditions for a flow.
     *
     * @codeCoverageIgnore
     */
    public function getEventFlowRule($eventFlowId): EventFlowRuleSimple
    {
        $request = $this->createRequestForGetEventFlowRule($eventFlowId);

        return $this->doRequestForGetEventFlowRule($request);
    }

    /**
     * Do the request for getEventFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetEventFlowRule(RequestInterface $request): EventFlowRuleSimple
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetEventFlowRule($responseData);
    }

    /**
     * Deserialize the responseData for getEventFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetEventFlowRule(ResponseData $response): EventFlowRuleSimple
    {
        return $this->serializer->deserialize($response, EventFlowRuleSimple::class, 'json');
    }

    /**
     * Create the RequestInterface for updateEventFlowRule.
     *
     * @param                     $eventFlowId         Flow ID
     * @param EventFlowRuleSimple $eventFlowRuleSimple Flow conditions
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateEventFlowRule(
        $eventFlowId,
        EventFlowRuleSimple $eventFlowRuleSimple
    ): RequestInterface {
        return $this->createRequest('PUT', sprintf('/%1$s/event_flows/%2$s/rule', $this->getOptions()->getAccountId(), $eventFlowId), $eventFlowRuleSimple, [], [], []);
    }

    /**
     * Set conditions for a flow.
     *
     * @codeCoverageIgnore
     */
    public function updateEventFlowRule($eventFlowId, EventFlowRuleSimple $eventFlowRuleSimple)
    {
        $request = $this->createRequestForUpdateEventFlowRule($eventFlowId, $eventFlowRuleSimple);

        return $this->doRequestForUpdateEventFlowRule($request);
    }

    /**
     * Do the request for updateEventFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateEventFlowRule(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getFilters.
     *
     * @param ReferenceRange $range     Limits the returned list
     * @param DateRange      $daterange Date range the filter was added in
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFilters(
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'sortorder' => $sortorder,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/filters', $this->getOptions()->getAccountId()), null, $matrices, $query, $headers);
    }

    /**
     * List filters per account.
     *
     * @codeCoverageIgnore
     */
    public function getFilters(
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null
    ): FilterCollection {
        $request = $this->createRequestForGetFilters($range, $daterange, $sortorder);

        return $this->doRequestForGetFilters($request);
    }

    /**
     * Do the request for getFilters.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFilters(RequestInterface $request): FilterCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFilters($responseData);
    }

    /**
     * Deserialize the responseData for getFilters.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFilters(ResponseData $response): FilterCollection
    {
        $items = $this->serializer->deserialize($response, FilterCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for deleteFilter.
     *
     * @param $filterId Filter ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteFilter($filterId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/filters/%2$s', $this->getOptions()->getAccountId(), $filterId), null, [], [], []);
    }

    /**
     * Delete a recipient from the filter.
     *
     * @codeCoverageIgnore
     */
    public function deleteFilter($filterId)
    {
        $request = $this->createRequestForDeleteFilter($filterId);

        return $this->doRequestForDeleteFilter($request);
    }

    /**
     * Do the request for deleteFilter.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteFilter(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlowRules(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/flow_rules', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * Get flow rule list for all flows.
     *
     * @codeCoverageIgnore
     */
    public function getFlowRules(): FlowRuleItemCollection
    {
        $request = $this->createRequestForGetFlowRules();

        return $this->doRequestForGetFlowRules($request);
    }

    /**
     * Do the request for getFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlowRules(RequestInterface $request): FlowRuleItemCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlowRules($responseData);
    }

    /**
     * Deserialize the responseData for getFlowRules.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlowRules(ResponseData $response): FlowRuleItemCollection
    {
        return $this->serializer->deserialize($response, FlowRuleItemCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getFlowTemplates.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlowTemplates(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/flow_templates', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * List flow templates per account.
     *
     * @codeCoverageIgnore
     */
    public function getFlowTemplates(): FlowTemplateCollection
    {
        $request = $this->createRequestForGetFlowTemplates();

        return $this->doRequestForGetFlowTemplates($request);
    }

    /**
     * Do the request for getFlowTemplates.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlowTemplates(RequestInterface $request): FlowTemplateCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlowTemplates($responseData);
    }

    /**
     * Deserialize the responseData for getFlowTemplates.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlowTemplates(ResponseData $response): FlowTemplateCollection
    {
        return $this->serializer->deserialize($response, FlowTemplateCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getFlows.
     *
     * @param bool $statistics Whether to return statistics per flow
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlows(?bool $statistics = true): RequestInterface
    {
        $query = [
            'statistics' => $statistics,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/flows', $this->getOptions()->getAccountId()), null, [], $query, []);
    }

    /**
     * List flows per account.
     *
     * @codeCoverageIgnore
     */
    public function getFlows(?bool $statistics = true): FlowCollection
    {
        $request = $this->createRequestForGetFlows($statistics);

        return $this->doRequestForGetFlows($request);
    }

    /**
     * Do the request for getFlows.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlows(RequestInterface $request): FlowCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlows($responseData);
    }

    /**
     * Deserialize the responseData for getFlows.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlows(ResponseData $response): FlowCollection
    {
        return $this->serializer->deserialize($response, FlowCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createFlow.
     *
     * @param Flow $flow Flow object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateFlow(Flow $flow): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/flows', $this->getOptions()->getAccountId()), $flow, [], [], []);
    }

    /**
     * Create a new flow.
     *
     * @codeCoverageIgnore
     */
    public function createFlow(Flow $flow)
    {
        $request = $this->createRequestForCreateFlow($flow);

        return $this->doRequestForCreateFlow($request);
    }

    /**
     * Do the request for createFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateFlow(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for deleteFlow.
     *
     * @param $flowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteFlow($flowId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/flows/%2$s', $this->getOptions()->getAccountId(), $flowId), null, [], [], []);
    }

    /**
     * Delete flow by id.
     *
     * @codeCoverageIgnore
     */
    public function deleteFlow($flowId)
    {
        $request = $this->createRequestForDeleteFlow($flowId);

        return $this->doRequestForDeleteFlow($request);
    }

    /**
     * Do the request for deleteFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteFlow(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getFlow.
     *
     * @param $flowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlow($flowId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/flows/%2$s', $this->getOptions()->getAccountId(), $flowId), null, [], [], []);
    }

    /**
     * Get flow by id.
     *
     * @codeCoverageIgnore
     */
    public function getFlow($flowId): Flow
    {
        $request = $this->createRequestForGetFlow($flowId);

        return $this->doRequestForGetFlow($request);
    }

    /**
     * Do the request for getFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlow(RequestInterface $request): Flow
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlow($responseData);
    }

    /**
     * Deserialize the responseData for getFlow.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlow(ResponseData $response): Flow
    {
        return $this->serializer->deserialize($response, Flow::class, 'json');
    }

    /**
     * Create the RequestInterface for updateFlow.
     *
     * @param      $flowId Flow ID
     * @param Flow $flow   Flow object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateFlow($flowId, Flow $flow): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/flows/%2$s', $this->getOptions()->getAccountId(), $flowId), $flow, [], [], []);
    }

    /**
     * Save flow.
     *
     * @codeCoverageIgnore
     */
    public function updateFlow($flowId, Flow $flow): Flow
    {
        $request = $this->createRequestForUpdateFlow($flowId, $flow);

        return $this->doRequestForUpdateFlow($request);
    }

    /**
     * Do the request for updateFlow.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateFlow(RequestInterface $request): Flow
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForUpdateFlow($responseData);
    }

    /**
     * Deserialize the responseData for updateFlow.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForUpdateFlow(ResponseData $response): Flow
    {
        return $this->serializer->deserialize($response, Flow::class, 'json');
    }

    /**
     * Create the RequestInterface for getFlowMessages.
     *
     * @param            $flowId     Flow ID
     * @param DateRange  $daterange  Date range the message was submitted in
     * @param ItemsRange $range      Limits the returned list
     * @param bool       $addheaders Whether to add e-mail headers
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlowMessages(
        $flowId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/flows/%2$s/messages', $this->getOptions()->getAccountId(), $flowId), null, $matrices, $query, $headers);
    }

    /**
     * List messages per flow.
     *
     * @codeCoverageIgnore
     */
    public function getFlowMessages(
        $flowId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request = $this->createRequestForGetFlowMessages($flowId, $daterange, $range, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetFlowMessages($request);
    }

    /**
     * Do the request for getFlowMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlowMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlowMessages($responseData);
    }

    /**
     * Deserialize the responseData for getFlowMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlowMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getFlowRule.
     *
     * @param $flowId Flow ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlowRule($flowId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/flows/%2$s/rule', $this->getOptions()->getAccountId(), $flowId), null, [], [], []);
    }

    /**
     * Get flow conditions for a flow.
     *
     * @codeCoverageIgnore
     */
    public function getFlowRule($flowId): FlowRuleSimple
    {
        $request = $this->createRequestForGetFlowRule($flowId);

        return $this->doRequestForGetFlowRule($request);
    }

    /**
     * Do the request for getFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlowRule(RequestInterface $request): FlowRuleSimple
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlowRule($responseData);
    }

    /**
     * Deserialize the responseData for getFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlowRule(ResponseData $response): FlowRuleSimple
    {
        return $this->serializer->deserialize($response, FlowRuleSimple::class, 'json');
    }

    /**
     * Create the RequestInterface for updateFlowRule.
     *
     * @param                $flowId         Flow ID
     * @param FlowRuleSimple $flowRuleSimple Flow conditions
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateFlowRule($flowId, FlowRuleSimple $flowRuleSimple): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/flows/%2$s/rule', $this->getOptions()->getAccountId(), $flowId), $flowRuleSimple, [], [], []);
    }

    /**
     * Set conditions for a flow.
     *
     * @codeCoverageIgnore
     */
    public function updateFlowRule($flowId, FlowRuleSimple $flowRuleSimple)
    {
        $request = $this->createRequestForUpdateFlowRule($flowId, $flowRuleSimple);

        return $this->doRequestForUpdateFlowRule($request);
    }

    /**
     * Do the request for updateFlowRule.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateFlowRule(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getFlowStats.
     *
     * @param           $flowId    Flow ID
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFlowStats($flowId, DateRange $daterange, ?int $interval = null): RequestInterface
    {
        $matrices = [
            'daterange' => $daterange,
            'interval'  => $interval,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/flows/%2$s/stats', $this->getOptions()->getAccountId(), $flowId), null, $matrices, [], []);
    }

    /**
     * Get time based message statistics for a message flow.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     *
     * @codeCoverageIgnore
     */
    public function getFlowStats($flowId, DateRange $daterange, ?int $interval = null): DataSets
    {
        $request = $this->createRequestForGetFlowStats($flowId, $daterange, $interval);

        return $this->doRequestForGetFlowStats($request);
    }

    /**
     * Do the request for getFlowStats.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetFlowStats(RequestInterface $request): DataSets
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetFlowStats($responseData);
    }

    /**
     * Deserialize the responseData for getFlowStats.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetFlowStats(ResponseData $response): DataSets
    {
        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageEvents.
     *
     * @param ReferenceRange $range          Limits the returned list
     * @param array          $flowIds        Filter results on message flow ID
     * @param array          $sourceIds      Filter results on message source ID
     * @param bool           $addmessagetags Message tags will be included with each event if this parameter is true
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageEvents(
        ReferenceRange $range,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?string $sortorder = null,
        ?bool $addmessagetags = false,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null
    ): RequestInterface {
        $matrices = [
            'flow_ids'      => $flowIds,
            'source_ids'    => $sourceIds,
            'daterange'     => $daterange,
            'receivedrange' => $receivedrange,
        ];
        $query = [
            'sortorder'      => $sortorder,
            'addmessagetags' => $addmessagetags,
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
        ReferenceRange $range,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?string $sortorder = null,
        ?bool $addmessagetags = false,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null
    ): MessageEventCollection {
        $request = $this->createRequestForGetMessageEvents($range, $flowIds, $sourceIds, $sortorder, $addmessagetags, $daterange, $receivedrange);

        return $this->doRequestForGetMessageEvents($request);
    }

    /**
     * Do the request for getMessageEvents.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageEvents(RequestInterface $request): MessageEventCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageEvents($responseData);
    }

    /**
     * Deserialize the responseData for getMessageEvents.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageEvents(ResponseData $response): MessageEventCollection
    {
        $items = $this->serializer->deserialize($response, MessageEventCollection::class, 'json');
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
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageHolds(ItemsRange $range, ?DateRange $daterange = null): RequestInterface
    {
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
    public function getMessageHolds(ItemsRange $range, ?DateRange $daterange = null): MessageHoldCollection
    {
        $request = $this->createRequestForGetMessageHolds($range, $daterange);

        return $this->doRequestForGetMessageHolds($request);
    }

    /**
     * Do the request for getMessageHolds.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageHolds(RequestInterface $request): MessageHoldCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageHolds($responseData);
    }

    /**
     * Deserialize the responseData for getMessageHolds.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageHolds(ResponseData $response): MessageHoldCollection
    {
        $items = $this->serializer->deserialize($response, MessageHoldCollection::class, 'json');
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
     *
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
        $request = $this->createRequestForGetMessageHold($messageId);

        return $this->doRequestForGetMessageHold($request);
    }

    /**
     * Do the request for getMessageHold.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageHold(RequestInterface $request): MessageHold
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageHold($responseData);
    }

    /**
     * Deserialize the responseData for getMessageHold.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageHold(ResponseData $response): MessageHold
    {
        return $this->serializer->deserialize($response, MessageHold::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessages.
     *
     * @param ReferenceRange $range      Limits the returned list
     * @param array          $flowIds    Filter results on flow ID
     * @param string         $sortfield  Sort by INSERTED or SUBMITTED (default INSERTED)
     * @param bool           $addevents  Whether to add message events
     * @param bool           $addheaders Whether to add e-mail headers
     * @param DateRange      $daterange  Date range the message was submitted in
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessages(
        ReferenceRange $range,
        ?array $flowIds = null,
        ?string $sortfield = null,
        ?string $sortorder = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?DateRange $daterange = null
    ): RequestInterface {
        $matrices = [
            'flow_ids'  => $flowIds,
            'daterange' => $daterange,
        ];
        $query = [
            'sortfield'     => $sortfield,
            'sortorder'     => $sortorder,
            'addevents'     => $addevents,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
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
        ReferenceRange $range,
        ?array $flowIds = null,
        ?string $sortfield = null,
        ?string $sortorder = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false,
        ?DateRange $daterange = null
    ): MessageCollection {
        $request = $this->createRequestForGetMessages($range, $flowIds, $sortfield, $sortorder, $addevents, $addheaders, $addonlinelink, $addtags, $daterange);

        return $this->doRequestForGetMessages($request);
    }

    /**
     * Do the request for getMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessages($responseData);
    }

    /**
     * Deserialize the responseData for getMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
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
        $request = $this->createRequestForSimulateMessage($simulateMessage);

        return $this->doRequestForSimulateMessage($request);
    }

    /**
     * Do the request for simulateMessage.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForSimulateMessage(RequestInterface $request): SimulateMessageResult
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForSimulateMessage($responseData);
    }

    /**
     * Deserialize the responseData for simulateMessage.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForSimulateMessage(ResponseData $response): SimulateMessageResult
    {
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
        $request = $this->createRequestForSubmitMessage($submitMessage);

        return $this->doRequestForSubmitMessage($request);
    }

    /**
     * Do the request for submitMessage.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForSubmitMessage(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getMessage.
     *
     * @param $messageId Message ID
     *
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
        $request = $this->createRequestForGetMessage($messageId, $addtags);

        return $this->doRequestForGetMessage($request);
    }

    /**
     * Do the request for getMessage.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessage(RequestInterface $request): Message
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessage($responseData);
    }

    /**
     * Deserialize the responseData for getMessage.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessage(ResponseData $response): Message
    {
        return $this->serializer->deserialize($response, Message::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageArchive.
     *
     * @param $messageId Message ID
     *
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
        $request = $this->createRequestForGetMessageArchive($messageId, $addattachments, $adddata);

        return $this->doRequestForGetMessageArchive($request);
    }

    /**
     * Do the request for getMessageArchive.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageArchive(RequestInterface $request): MessageArchiveCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageArchive($responseData);
    }

    /**
     * Deserialize the responseData for getMessageArchive.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageArchive(ResponseData $response): MessageArchiveCollection
    {
        return $this->serializer->deserialize($response, MessageArchiveCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageArchiveAttachment.
     *
     * @param $messageId  Message ID
     * @param $flowStepId Flow step ID
     * @param $contentId  Attachment content ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageArchiveAttachment($messageId, $flowStepId, $contentId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/messages/%4$s/archive/%3$s/attachment/%2$s', $this->getOptions()->getAccountId(), $contentId, $flowStepId, $messageId), null, [], [], []);
    }

    /**
     * Fetch an attachment including data for an archived message.
     *
     * @codeCoverageIgnore
     */
    public function getMessageArchiveAttachment($messageId, $flowStepId, $contentId): Attachment
    {
        $request = $this->createRequestForGetMessageArchiveAttachment($messageId, $flowStepId, $contentId);

        return $this->doRequestForGetMessageArchiveAttachment($request);
    }

    /**
     * Do the request for getMessageArchiveAttachment.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageArchiveAttachment(RequestInterface $request): Attachment
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageArchiveAttachment($responseData);
    }

    /**
     * Deserialize the responseData for getMessageArchiveAttachment.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageArchiveAttachment(ResponseData $response): Attachment
    {
        return $this->serializer->deserialize($response, Attachment::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageErrorArchive.
     *
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
     * Get message error archive.
     *
     * @codeCoverageIgnore
     */
    public function getMessageErrorArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): MessageArchive {
        $request = $this->createRequestForGetMessageErrorArchive($messageId, $addattachments, $adddata);

        return $this->doRequestForGetMessageErrorArchive($request);
    }

    /**
     * Do the request for getMessageErrorArchive.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageErrorArchive(RequestInterface $request): MessageArchive
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageErrorArchive($responseData);
    }

    /**
     * Deserialize the responseData for getMessageErrorArchive.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageErrorArchive(ResponseData $response): MessageArchive
    {
        return $this->serializer->deserialize($response, MessageArchive::class, 'json');
    }

    /**
     * Create the RequestInterface for resendMessage.
     *
     * @param $messageId Message ID
     *
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
        $request = $this->createRequestForResendMessage($messageId, $resendMessage);

        return $this->doRequestForResendMessage($request);
    }

    /**
     * Do the request for resendMessage.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForResendMessage(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getMessageStats.
     *
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageStats(
        DateRange $daterange,
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
    public function getMessageStats(DateRange $daterange, ?array $flowIds = null, ?int $interval = null): DataSets
    {
        $request = $this->createRequestForGetMessageStats($daterange, $flowIds, $interval);

        return $this->doRequestForGetMessageStats($request);
    }

    /**
     * Do the request for getMessageStats.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetMessageStats(RequestInterface $request): DataSets
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetMessageStats($responseData);
    }

    /**
     * Deserialize the responseData for getMessageStats.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetMessageStats(ResponseData $response): DataSets
    {
        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getRecipient.
     *
     * @param           $recipient Recipient email address or phone number
     * @param DateRange $daterange Specifies the date range for message statistics
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRecipient($recipient, ?DateRange $daterange = null): RequestInterface
    {
        $matrices = [
            'daterange' => $daterange,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/recipient/%2$s', $this->getOptions()->getAccountId(), $recipient), null, $matrices, [], []);
    }

    /**
     * Get information about a recipient.
     *
     *  Message statistics are only included if a date range is specified.
     *
     * @codeCoverageIgnore
     */
    public function getRecipient($recipient, ?DateRange $daterange = null): Recipient
    {
        $request = $this->createRequestForGetRecipient($recipient, $daterange);

        return $this->doRequestForGetRecipient($request);
    }

    /**
     * Do the request for getRecipient.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetRecipient(RequestInterface $request): Recipient
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetRecipient($responseData);
    }

    /**
     * Deserialize the responseData for getRecipient.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetRecipient(ResponseData $response): Recipient
    {
        return $this->serializer->deserialize($response, Recipient::class, 'json');
    }

    /**
     * Create the RequestInterface for getRecipientMessages.
     *
     * @param                $recipient     Recipient email address or phone number
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRecipientMessages(
        $recipient,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'sortorder'     => $sortorder,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/recipient/%2$s/messages', $this->getOptions()->getAccountId(), $recipient), null, $matrices, $query, $headers);
    }

    /**
     * List messages per recipient.
     *
     * @codeCoverageIgnore
     */
    public function getRecipientMessages(
        $recipient,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request = $this->createRequestForGetRecipientMessages($recipient, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetRecipientMessages($request);
    }

    /**
     * Do the request for getRecipientMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetRecipientMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetRecipientMessages($responseData);
    }

    /**
     * Deserialize the responseData for getRecipientMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetRecipientMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getRoles.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRoles(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/roles', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * Get roles.
     *
     * @codeCoverageIgnore
     */
    public function getRoles(): RoleCollection
    {
        $request = $this->createRequestForGetRoles();

        return $this->doRequestForGetRoles($request);
    }

    /**
     * Do the request for getRoles.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetRoles(RequestInterface $request): RoleCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetRoles($responseData);
    }

    /**
     * Deserialize the responseData for getRoles.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetRoles(ResponseData $response): RoleCollection
    {
        return $this->serializer->deserialize($response, RoleCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for getSenderMessages.
     *
     * @param                $sender        Sender email address or phone number
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSenderMessages(
        $sender,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'sortorder'     => $sortorder,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sender/%2$s/messages', $this->getOptions()->getAccountId(), $sender), null, $matrices, $query, $headers);
    }

    /**
     * List messages per sender.
     *
     * @codeCoverageIgnore
     */
    public function getSenderMessages(
        $sender,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request = $this->createRequestForGetSenderMessages($sender, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetSenderMessages($request);
    }

    /**
     * Do the request for getSenderMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSenderMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSenderMessages($responseData);
    }

    /**
     * Deserialize the responseData for getSenderMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSenderMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getSenderDomains.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSenderDomains(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/sender_domains', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * List sender domains by account.
     *
     * @codeCoverageIgnore
     */
    public function getSenderDomains(): SenderDomainCollection
    {
        $request = $this->createRequestForGetSenderDomains();

        return $this->doRequestForGetSenderDomains($request);
    }

    /**
     * Do the request for getSenderDomains.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSenderDomains(RequestInterface $request): SenderDomainCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSenderDomains($responseData);
    }

    /**
     * Deserialize the responseData for getSenderDomains.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSenderDomains(ResponseData $response): SenderDomainCollection
    {
        return $this->serializer->deserialize($response, SenderDomainCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateSenderDomain(SenderDomain $senderDomain): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/sender_domains', $this->getOptions()->getAccountId()), $senderDomain, [], [], []);
    }

    /**
     * Create sender domain.
     *
     * @codeCoverageIgnore
     */
    public function createSenderDomain(SenderDomain $senderDomain)
    {
        $request = $this->createRequestForCreateSenderDomain($senderDomain);

        return $this->doRequestForCreateSenderDomain($request);
    }

    /**
     * Do the request for createSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateSenderDomain(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSenderDomainsByDomain.
     *
     * @param      $domain   Sender domain name
     * @param bool $validate Validate DNS records for this SenderDomain
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSenderDomainsByDomain($domain, ?bool $validate = false): RequestInterface
    {
        $query = [
            'validate' => $validate,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sender_domains/by_domain/%2$s', $this->getOptions()->getAccountId(), $domain), null, [], $query, []);
    }

    /**
     * Get sender domain by domain name.
     *
     * @codeCoverageIgnore
     */
    public function getSenderDomainsByDomain($domain, ?bool $validate = false): SenderDomain
    {
        $request = $this->createRequestForGetSenderDomainsByDomain($domain, $validate);

        return $this->doRequestForGetSenderDomainsByDomain($request);
    }

    /**
     * Do the request for getSenderDomainsByDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSenderDomainsByDomain(RequestInterface $request): SenderDomain
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSenderDomainsByDomain($responseData);
    }

    /**
     * Deserialize the responseData for getSenderDomainsByDomain.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSenderDomainsByDomain(ResponseData $response): SenderDomain
    {
        return $this->serializer->deserialize($response, SenderDomain::class, 'json');
    }

    /**
     * Create the RequestInterface for validateSenderDomain.
     *
     * @param SenderDomain $senderDomain the sender domain to validate
     *
     * @codeCoverageIgnore
     */
    public function createRequestForValidateSenderDomain(SenderDomain $senderDomain): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/sender_domains/validate', $this->getOptions()->getAccountId()), $senderDomain, [], [], []);
    }

    /**
     * Validates but does not save a sender domain.
     *
     * @codeCoverageIgnore
     */
    public function validateSenderDomain(SenderDomain $senderDomain): SenderDomain
    {
        $request = $this->createRequestForValidateSenderDomain($senderDomain);

        return $this->doRequestForValidateSenderDomain($request);
    }

    /**
     * Do the request for validateSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForValidateSenderDomain(RequestInterface $request): SenderDomain
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForValidateSenderDomain($responseData);
    }

    /**
     * Deserialize the responseData for validateSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForValidateSenderDomain(ResponseData $response): SenderDomain
    {
        return $this->serializer->deserialize($response, SenderDomain::class, 'json');
    }

    /**
     * Create the RequestInterface for deleteSenderDomain.
     *
     * @param $domainId Sender domain ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteSenderDomain($domainId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/sender_domains/%2$s', $this->getOptions()->getAccountId(), $domainId), null, [], [], []);
    }

    /**
     * Delete sender domain.
     *
     * @codeCoverageIgnore
     */
    public function deleteSenderDomain($domainId)
    {
        $request = $this->createRequestForDeleteSenderDomain($domainId);

        return $this->doRequestForDeleteSenderDomain($request);
    }

    /**
     * Do the request for deleteSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteSenderDomain(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSenderDomain.
     *
     * @param      $domainId Sender domain ID
     * @param bool $validate Validate DNS records for this SenderDomain
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSenderDomain($domainId, ?bool $validate = false): RequestInterface
    {
        $query = [
            'validate' => $validate,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sender_domains/%2$s', $this->getOptions()->getAccountId(), $domainId), null, [], $query, []);
    }

    /**
     * Get sender domain by id.
     *
     * @codeCoverageIgnore
     */
    public function getSenderDomain($domainId, ?bool $validate = false): SenderDomain
    {
        $request = $this->createRequestForGetSenderDomain($domainId, $validate);

        return $this->doRequestForGetSenderDomain($request);
    }

    /**
     * Do the request for getSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSenderDomain(RequestInterface $request): SenderDomain
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSenderDomain($responseData);
    }

    /**
     * Deserialize the responseData for getSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSenderDomain(ResponseData $response): SenderDomain
    {
        return $this->serializer->deserialize($response, SenderDomain::class, 'json');
    }

    /**
     * Create the RequestInterface for updateSenderDomain.
     *
     * @param $domainId Sender domain ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateSenderDomain($domainId, SenderDomain $senderDomain): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/sender_domains/%2$s', $this->getOptions()->getAccountId(), $domainId), $senderDomain, [], [], []);
    }

    /**
     * Save sender domain.
     *
     * @codeCoverageIgnore
     */
    public function updateSenderDomain($domainId, SenderDomain $senderDomain)
    {
        $request = $this->createRequestForUpdateSenderDomain($domainId, $senderDomain);

        return $this->doRequestForUpdateSenderDomain($request);
    }

    /**
     * Do the request for updateSenderDomain.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateSenderDomain(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSources.
     *
     * @param bool $statistics Whether to include message statistics or not
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSources(?bool $statistics = true): RequestInterface
    {
        $query = [
            'statistics' => $statistics,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sources', $this->getOptions()->getAccountId()), null, [], $query, []);
    }

    /**
     * List source systems per account.
     *
     * @codeCoverageIgnore
     */
    public function getSources(?bool $statistics = true): SourceCollection
    {
        $request = $this->createRequestForGetSources($statistics);

        return $this->doRequestForGetSources($request);
    }

    /**
     * Do the request for getSources.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSources(RequestInterface $request): SourceCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSources($responseData);
    }

    /**
     * Deserialize the responseData for getSources.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSources(ResponseData $response): SourceCollection
    {
        return $this->serializer->deserialize($response, SourceCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createSource.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateSource(Source $source): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/sources', $this->getOptions()->getAccountId()), $source, [], [], []);
    }

    /**
     * Create a new source.
     *
     * @codeCoverageIgnore
     */
    public function createSource(Source $source)
    {
        $request = $this->createRequestForCreateSource($source);

        return $this->doRequestForCreateSource($request);
    }

    /**
     * Do the request for createSource.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateSource(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for deleteSource.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteSource($sourceId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/sources/%2$s', $this->getOptions()->getAccountId(), $sourceId), null, [], [], []);
    }

    /**
     * Delete a source.
     *
     * @codeCoverageIgnore
     */
    public function deleteSource($sourceId)
    {
        $request = $this->createRequestForDeleteSource($sourceId);

        return $this->doRequestForDeleteSource($request);
    }

    /**
     * Do the request for deleteSource.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteSource(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSource.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSource($sourceId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/sources/%2$s', $this->getOptions()->getAccountId(), $sourceId), null, [], [], []);
    }

    /**
     * Get a source by id.
     *
     * @codeCoverageIgnore
     */
    public function getSource($sourceId): Source
    {
        $request = $this->createRequestForGetSource($sourceId);

        return $this->doRequestForGetSource($request);
    }

    /**
     * Do the request for getSource.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSource(RequestInterface $request): Source
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSource($responseData);
    }

    /**
     * Deserialize the responseData for getSource.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSource(ResponseData $response): Source
    {
        return $this->serializer->deserialize($response, Source::class, 'json');
    }

    /**
     * Create the RequestInterface for updateSource.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateSource($sourceId, Source $source): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/sources/%2$s', $this->getOptions()->getAccountId(), $sourceId), $source, [], [], []);
    }

    /**
     * Update a source.
     *
     * @codeCoverageIgnore
     */
    public function updateSource($sourceId, Source $source)
    {
        $request = $this->createRequestForUpdateSource($sourceId, $source);

        return $this->doRequestForUpdateSource($request);
    }

    /**
     * Do the request for updateSource.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateSource(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSourceMessages.
     *
     * @param            $sourceId   Source ID
     * @param DateRange  $daterange  Date range the message was submitted in
     * @param ItemsRange $range      Limits the returned list
     * @param bool       $addheaders Whether to add e-mail headers
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSourceMessages(
        $sourceId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sources/%2$s/messages', $this->getOptions()->getAccountId(), $sourceId), null, $matrices, $query, $headers);
    }

    /**
     * List messages per source.
     *
     * @codeCoverageIgnore
     */
    public function getSourceMessages(
        $sourceId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request = $this->createRequestForGetSourceMessages($sourceId, $daterange, $range, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetSourceMessages($request);
    }

    /**
     * Do the request for getSourceMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSourceMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSourceMessages($responseData);
    }

    /**
     * Deserialize the responseData for getSourceMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSourceMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getSourceStats.
     *
     * @param           $sourceId  Source ID
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSourceStats(
        $sourceId,
        DateRange $daterange,
        ?int $interval = null
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
            'interval'  => $interval,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/sources/%2$s/stats', $this->getOptions()->getAccountId(), $sourceId), null, $matrices, [], []);
    }

    /**
     * Get time based message statistics for a message source.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     *
     * @codeCoverageIgnore
     */
    public function getSourceStats($sourceId, DateRange $daterange, ?int $interval = null): DataSets
    {
        $request = $this->createRequestForGetSourceStats($sourceId, $daterange, $interval);

        return $this->doRequestForGetSourceStats($request);
    }

    /**
     * Do the request for getSourceStats.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSourceStats(RequestInterface $request): DataSets
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSourceStats($responseData);
    }

    /**
     * Deserialize the responseData for getSourceStats.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSourceStats(ResponseData $response): DataSets
    {
        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getSourceUsers.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSourceUsers($sourceId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/sources/%2$s/users', $this->getOptions()->getAccountId(), $sourceId), null, [], [], []);
    }

    /**
     * List credentials per source system.
     *
     * @codeCoverageIgnore
     */
    public function getSourceUsers($sourceId): CredentialsCollection
    {
        $request = $this->createRequestForGetSourceUsers($sourceId);

        return $this->doRequestForGetSourceUsers($request);
    }

    /**
     * Do the request for getSourceUsers.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSourceUsers(RequestInterface $request): CredentialsCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSourceUsers($responseData);
    }

    /**
     * Deserialize the responseData for getSourceUsers.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSourceUsers(ResponseData $response): CredentialsCollection
    {
        return $this->serializer->deserialize($response, CredentialsCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createSourceUsers.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateSourceUsers($sourceId, Credentials $credentials): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/sources/%2$s/users', $this->getOptions()->getAccountId(), $sourceId), $credentials, [], [], []);
    }

    /**
     * Create credentials for a source.
     *
     * @codeCoverageIgnore
     */
    public function createSourceUsers($sourceId, Credentials $credentials): Credentials
    {
        $request = $this->createRequestForCreateSourceUsers($sourceId, $credentials);

        return $this->doRequestForCreateSourceUsers($request);
    }

    /**
     * Do the request for createSourceUsers.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateSourceUsers(RequestInterface $request): Credentials
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForCreateSourceUsers($responseData);
    }

    /**
     * Deserialize the responseData for createSourceUsers.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForCreateSourceUsers(ResponseData $response): Credentials
    {
        return $this->serializer->deserialize($response, Credentials::class, 'json');
    }

    /**
     * Create the RequestInterface for deleteSourceUser.
     *
     * @param $sourceId Source ID
     * @param $userId   Credential ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteSourceUser($sourceId, $userId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/sources/%2$s/users/%3$s', $this->getOptions()->getAccountId(), $sourceId, $userId), null, [], [], []);
    }

    /**
     * Delete credentials.
     *
     * @codeCoverageIgnore
     */
    public function deleteSourceUser($sourceId, $userId)
    {
        $request = $this->createRequestForDeleteSourceUser($sourceId, $userId);

        return $this->doRequestForDeleteSourceUser($request);
    }

    /**
     * Do the request for deleteSourceUser.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteSourceUser(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getSourceUser.
     *
     * @param $sourceId Source ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSourceUser($sourceId, $userId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/sources/%2$s/users/%3$s', $this->getOptions()->getAccountId(), $sourceId, $userId), null, [], [], []);
    }

    /**
     * Get credentials for a source.
     *
     * @codeCoverageIgnore
     */
    public function getSourceUser($sourceId, $userId): Credentials
    {
        $request = $this->createRequestForGetSourceUser($sourceId, $userId);

        return $this->doRequestForGetSourceUser($request);
    }

    /**
     * Do the request for getSourceUser.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetSourceUser(RequestInterface $request): Credentials
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetSourceUser($responseData);
    }

    /**
     * Deserialize the responseData for getSourceUser.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetSourceUser(ResponseData $response): Credentials
    {
        return $this->serializer->deserialize($response, Credentials::class, 'json');
    }

    /**
     * Create the RequestInterface for updateSourceUser.
     *
     * @param $sourceId Source ID
     * @param $userId   Credential ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateSourceUser($sourceId, $userId, Credentials $credentials): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/sources/%2$s/users/%3$s', $this->getOptions()->getAccountId(), $sourceId, $userId), $credentials, [], [], []);
    }

    /**
     * Update credentials for a source.
     *
     * @codeCoverageIgnore
     */
    public function updateSourceUser($sourceId, $userId, Credentials $credentials): Credentials
    {
        $request = $this->createRequestForUpdateSourceUser($sourceId, $userId, $credentials);

        return $this->doRequestForUpdateSourceUser($request);
    }

    /**
     * Do the request for updateSourceUser.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateSourceUser(RequestInterface $request): Credentials
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForUpdateSourceUser($responseData);
    }

    /**
     * Deserialize the responseData for updateSourceUser.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForUpdateSourceUser(ResponseData $response): Credentials
    {
        return $this->serializer->deserialize($response, Credentials::class, 'json');
    }

    /**
     * Create the RequestInterface for getTagMessages.
     *
     * @param                $tag           Tag
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetTagMessages(
        $tag,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange' => $daterange,
        ];
        $query = [
            'sortorder'     => $sortorder,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
        ];
        $headers = [
            'Range' => $range,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/tag/%2$s/messages', $this->getOptions()->getAccountId(), $tag), null, $matrices, $query, $headers);
    }

    /**
     * List messages per tag.
     *
     * @codeCoverageIgnore
     */
    public function getTagMessages(
        $tag,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request = $this->createRequestForGetTagMessages($tag, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetTagMessages($request);
    }

    /**
     * Do the request for getTagMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetTagMessages(RequestInterface $request): MessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetTagMessages($responseData);
    }

    /**
     * Deserialize the responseData for getTagMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetTagMessages(ResponseData $response): MessageCollection
    {
        $items = $this->serializer->deserialize($response, MessageCollection::class, 'json');
        if ($response->getMeta('next-range') instanceof ReferenceRange) {
            $items->setNextRange($response->getMeta('next-range'));
        }
        if ($response->getMeta('content-range') instanceof ContentRange) {
            $items->setContentRange($response->getMeta('content-range'));
        }

        return $items;
    }

    /**
     * Create the RequestInterface for getTemplates.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetTemplates(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/templates', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * List templates by account.
     *
     * @codeCoverageIgnore
     */
    public function getTemplates(): TemplateCollection
    {
        $request = $this->createRequestForGetTemplates();

        return $this->doRequestForGetTemplates($request);
    }

    /**
     * Do the request for getTemplates.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetTemplates(RequestInterface $request): TemplateCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetTemplates($responseData);
    }

    /**
     * Deserialize the responseData for getTemplates.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetTemplates(ResponseData $response): TemplateCollection
    {
        return $this->serializer->deserialize($response, TemplateCollection::class, 'json');
    }

    /**
     * Create the RequestInterface for createTemplate.
     *
     * @param Template $template Template object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForCreateTemplate(Template $template): RequestInterface
    {
        return $this->createRequest('POST', sprintf('/%1$s/templates', $this->getOptions()->getAccountId()), $template, [], [], []);
    }

    /**
     * Create template.
     *
     * @codeCoverageIgnore
     */
    public function createTemplate(Template $template)
    {
        $request = $this->createRequestForCreateTemplate($template);

        return $this->doRequestForCreateTemplate($request);
    }

    /**
     * Do the request for createTemplate.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForCreateTemplate(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for deleteTemplate.
     *
     * @param $templateId Template ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForDeleteTemplate($templateId): RequestInterface
    {
        return $this->createRequest('DELETE', sprintf('/%1$s/templates/%2$s', $this->getOptions()->getAccountId(), $templateId), null, [], [], []);
    }

    /**
     * Delete template by id.
     *
     * @codeCoverageIgnore
     */
    public function deleteTemplate($templateId)
    {
        $request = $this->createRequestForDeleteTemplate($templateId);

        return $this->doRequestForDeleteTemplate($request);
    }

    /**
     * Do the request for deleteTemplate.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForDeleteTemplate(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getTemplate.
     *
     * @param $templateId Template ID
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetTemplate($templateId): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/templates/%2$s', $this->getOptions()->getAccountId(), $templateId), null, [], [], []);
    }

    /**
     * Get template by id.
     *
     * @codeCoverageIgnore
     */
    public function getTemplate($templateId): Template
    {
        $request = $this->createRequestForGetTemplate($templateId);

        return $this->doRequestForGetTemplate($request);
    }

    /**
     * Do the request for getTemplate.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetTemplate(RequestInterface $request): Template
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetTemplate($responseData);
    }

    /**
     * Deserialize the responseData for getTemplate.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetTemplate(ResponseData $response): Template
    {
        return $this->serializer->deserialize($response, Template::class, 'json');
    }

    /**
     * Create the RequestInterface for updateTemplate.
     *
     * @param          $templateId Template ID
     * @param Template $template   Template object
     *
     * @codeCoverageIgnore
     */
    public function createRequestForUpdateTemplate($templateId, Template $template): RequestInterface
    {
        return $this->createRequest('PUT', sprintf('/%1$s/templates/%2$s', $this->getOptions()->getAccountId(), $templateId), $template, [], [], []);
    }

    /**
     * Save template.
     *
     * @codeCoverageIgnore
     */
    public function updateTemplate($templateId, Template $template)
    {
        $request = $this->createRequestForUpdateTemplate($templateId, $template);

        return $this->doRequestForUpdateTemplate($request);
    }

    /**
     * Do the request for updateTemplate.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForUpdateTemplate(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }

    /**
     * Create the RequestInterface for getUndeliveredMessages.
     *
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the message was submitted in
     * @param DateRange      $receivedrange Date range the message bounced
     * @param bool           $addevents     Whether to add message events
     * @param bool           $addheaders    Whether to add e-mail headers
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetUndeliveredMessages(
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null,
        ?string $sortorder = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface {
        $matrices = [
            'daterange'     => $daterange,
            'receivedrange' => $receivedrange,
        ];
        $query = [
            'sortorder'     => $sortorder,
            'addevents'     => $addevents,
            'addheaders'    => $addheaders,
            'addonlinelink' => $addonlinelink,
            'addtags'       => $addtags,
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
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null,
        ?string $sortorder = null,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): BouncedMessageCollection {
        $request = $this->createRequestForGetUndeliveredMessages($range, $daterange, $receivedrange, $sortorder, $addevents, $addheaders, $addonlinelink, $addtags);

        return $this->doRequestForGetUndeliveredMessages($request);
    }

    /**
     * Do the request for getUndeliveredMessages.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetUndeliveredMessages(RequestInterface $request): BouncedMessageCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetUndeliveredMessages($responseData);
    }

    /**
     * Deserialize the responseData for getUndeliveredMessages.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetUndeliveredMessages(ResponseData $response): BouncedMessageCollection
    {
        $items = $this->serializer->deserialize($response, BouncedMessageCollection::class, 'json');
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
     * Get users.
     *
     * @codeCoverageIgnore
     */
    public function getUsers(): AccountUserCollection
    {
        $request = $this->createRequestForGetUsers();

        return $this->doRequestForGetUsers($request);
    }

    /**
     * Do the request for getUsers.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForGetUsers(RequestInterface $request): AccountUserCollection
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->processResponseDataForGetUsers($responseData);
    }

    /**
     * Deserialize the responseData for getUsers.
     *
     * @codeCoverageIgnore
     */
    public function processResponseDataForGetUsers(ResponseData $response): AccountUserCollection
    {
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
        $request = $this->createRequestForAddUser($accountUser);

        return $this->doRequestForAddUser($request);
    }

    /**
     * Do the request for addUser.
     *
     * @codeCoverageIgnore
     */
    public function doRequestForAddUser(RequestInterface $request)
    {
        $responseData = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $responseData;
    }
}
