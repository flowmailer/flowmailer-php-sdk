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
     * Create the RequestInterface for getApiCredentials.
     *
     * @param int $sourceId
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetApiCredentials(?int $sourceId): RequestInterface
    {
        $matrices = [
            'source_id' => $sourceId,
        ];

        return $this->createRequest('GET', sprintf('/%1$s/api_credentials', $this->getOptions()->getAccountId()), null, $matrices, [], []);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getApiCredentials(?int $sourceId): CredentialsCollection
    {
        $request  = $this->createRequestForGetApiCredentials($sourceId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @codeCoverageIgnore
     */
    public function createApiCredentials(Credentials $credentials): Credentials
    {
        $request  = $this->createRequestForCreateApiCredentials($credentials);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @codeCoverageIgnore
     */
    public function deleteClientApiCredentials($clientId)
    {
        $request  = $this->createRequestForDeleteClientApiCredentials($clientId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
     * @codeCoverageIgnore
     */
    public function getClientApiCredentials($clientId): Credentials
    {
        $request  = $this->createRequestForGetClientApiCredentials($clientId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @codeCoverageIgnore
     */
    public function updateClientApiCredentials($clientId, Credentials $credentials)
    {
        $request  = $this->createRequestForUpdateClientApiCredentials($clientId, $credentials);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetEventFlowRules();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetEventFlowRulesHierarchy();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetEventFlows();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateEventFlow($eventFlow);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForDeleteEventFlow($eventFlowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetEventFlow($eventFlowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateEventFlow($eventFlowId, $eventFlow);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetEventFlowRule($eventFlowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateEventFlowRule($eventFlowId, $eventFlowRuleSimple);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }

    /**
     * Create the RequestInterface for getFilters.
     *
     * @param ReferenceRange $range     Limits the returned list
     * @param DateRange      $daterange Date range the filter was added in
     * @param string         $sortorder
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetFilters(
        ReferenceRange $range,
        ?DateRange $daterange,
        ?string $sortorder
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
    public function getFilters(ReferenceRange $range, ?DateRange $daterange, ?string $sortorder): FilterCollection
    {
        $request  = $this->createRequestForGetFilters($range, $daterange, $sortorder);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());
        $items    = $this->serializer->deserialize($response, FilterCollection::class, 'json');
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
        $request  = $this->createRequestForDeleteFilter($filterId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetFlowRules();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetFlowTemplates();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetFlows($statistics);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateFlow($flow);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForDeleteFlow($flowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetFlow($flowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateFlow($flowId, $flow);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, Flow::class, 'json');
    }

    /**
     * Create the RequestInterface for getFlowMessages.
     *
     * @param            $flowId        Flow ID
     * @param DateRange  $daterange     Date range the message was submitted in
     * @param ItemsRange $range         Limits the returned list
     * @param bool       $addheaders    Whether to add e-mail headers
     * @param bool       $addonlinelink
     * @param bool       $addtags
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
        $request  = $this->createRequestForGetFlowMessages($flowId, $daterange, $range, $addheaders, $addonlinelink, $addtags);
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
        $request  = $this->createRequestForGetFlowRule($flowId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateFlowRule($flowId, $flowRuleSimple);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
    public function createRequestForGetFlowStats($flowId, DateRange $daterange, ?int $interval): RequestInterface
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
    public function getFlowStats($flowId, DateRange $daterange, ?int $interval): DataSets
    {
        $request  = $this->createRequestForGetFlowStats($flowId, $daterange, $interval);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageEvents.
     *
     * @param ReferenceRange $range          Limits the returned list
     * @param array          $flowIds        Filter results on message flow ID
     * @param array          $sourceIds      Filter results on message source ID
     * @param bool           $addmessagetags Message tags will be included with each event if this parameter is true
     * @param string         $sortorder
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageEvents(
        ReferenceRange $range,
        ?array $flowIds,
        ?array $sourceIds,
        ?string $sortorder,
        ?bool $addmessagetags = false
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
        ReferenceRange $range,
        ?array $flowIds,
        ?array $sourceIds,
        ?string $sortorder,
        ?bool $addmessagetags = false
    ): MessageEventCollection {
        $request  = $this->createRequestForGetMessageEvents($range, $flowIds, $sourceIds, $sortorder, $addmessagetags);
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
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageHolds(ItemsRange $range, ?DateRange $daterange): RequestInterface
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
    public function getMessageHolds(ItemsRange $range, ?DateRange $daterange): MessageHoldCollection
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
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessages(
        ReferenceRange $range,
        ?array $flowIds,
        ?string $sortfield,
        ?string $sortorder,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
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
        ReferenceRange $range,
        ?array $flowIds,
        ?string $sortfield,
        ?string $sortorder,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request  = $this->createRequestForGetMessages($range, $flowIds, $sortfield, $sortorder, $addevents, $addheaders, $addonlinelink, $addtags);
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
        $request  = $this->createRequestForGetMessageArchive($messageId, $addattachments, $adddata);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetMessageArchiveAttachment($messageId, $flowStepId, $contentId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, Attachment::class, 'json');
    }

    /**
     * Create the RequestInterface for getMessageErrorArchive.
     *
     * @param bool $addattachments
     * @param bool $adddata
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
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetMessageStats(
        DateRange $daterange,
        ?array $flowIds,
        ?int $interval
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
    public function getMessageStats(DateRange $daterange, ?array $flowIds, ?int $interval): DataSets
    {
        $request  = $this->createRequestForGetMessageStats($daterange, $flowIds, $interval);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $this->serializer->deserialize($response, DataSets::class, 'json');
    }

    /**
     * Create the RequestInterface for getRecipient.
     *
     * @param $recipient Recipient email address or phone number
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRecipient($recipient): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/recipient/%2$s', $this->getOptions()->getAccountId(), $recipient), null, [], [], []);
    }

    /**
     * Get information about a recipient.
     *
     *  Message statistics are only included if a date range is specified.
     *
     * @codeCoverageIgnore
     */
    public function getRecipient($recipient): Recipient
    {
        $request  = $this->createRequestForGetRecipient($recipient);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @param bool           $addtags
     * @param string         $sortorder
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRecipientMessages(
        $recipient,
        ReferenceRange $range,
        ?DateRange $daterange,
        ?string $sortorder,
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
            'sortorder'     => $sortorder,
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
        ?DateRange $daterange,
        ?string $sortorder,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request  = $this->createRequestForGetRecipientMessages($recipient, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);
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
     * Create the RequestInterface for getRoles.
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetRoles(): RequestInterface
    {
        return $this->createRequest('GET', sprintf('/%1$s/roles', $this->getOptions()->getAccountId()), null, [], [], []);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRoles(): RoleCollection
    {
        $request  = $this->createRequestForGetRoles();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @param bool           $addtags
     * @param string         $sortorder
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSenderMessages(
        $sender,
        ReferenceRange $range,
        ?DateRange $daterange,
        ?string $sortorder,
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
            'sortorder'     => $sortorder,
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
        ?DateRange $daterange,
        ?string $sortorder,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request  = $this->createRequestForGetSenderMessages($sender, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);
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
        $request  = $this->createRequestForGetSenderDomains();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateSenderDomain($senderDomain);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetSenderDomainsByDomain($domain, $validate);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForValidateSenderDomain($senderDomain);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForDeleteSenderDomain($domainId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetSenderDomain($domainId, $validate);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateSenderDomain($domainId, $senderDomain);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetSources($statistics);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateSource($source);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForDeleteSource($sourceId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetSource($sourceId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateSource($sourceId, $source);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
    }

    /**
     * Create the RequestInterface for getSourceMessages.
     *
     * @param            $sourceId      Source ID
     * @param DateRange  $daterange     Date range the message was submitted in
     * @param ItemsRange $range         Limits the returned list
     * @param bool       $addheaders    Whether to add e-mail headers
     * @param bool       $addonlinelink
     * @param bool       $addtags
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
        $request  = $this->createRequestForGetSourceMessages($sourceId, $daterange, $range, $addheaders, $addonlinelink, $addtags);
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
     * Create the RequestInterface for getSourceStats.
     *
     * @param           $sourceId  Source ID
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetSourceStats($sourceId, DateRange $daterange, ?int $interval): RequestInterface
    {
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
    public function getSourceStats($sourceId, DateRange $daterange, ?int $interval): DataSets
    {
        $request  = $this->createRequestForGetSourceStats($sourceId, $daterange, $interval);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForGetSourceUsers($sourceId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateSourceUsers($sourceId, $credentials);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForDeleteSourceUser($sourceId, $userId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetSourceUser($sourceId, $userId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateSourceUser($sourceId, $userId, $credentials);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
     * @param bool           $addtags
     * @param string         $sortorder
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetTagMessages(
        $tag,
        ReferenceRange $range,
        ?DateRange $daterange,
        ?string $sortorder,
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
            'sortorder'     => $sortorder,
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
        ?DateRange $daterange,
        ?string $sortorder,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection {
        $request  = $this->createRequestForGetTagMessages($tag, $range, $daterange, $sortorder, $addheaders, $addonlinelink, $addtags);
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
        $request  = $this->createRequestForGetTemplates();
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForCreateTemplate($template);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForDeleteTemplate($templateId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
        $request  = $this->createRequestForGetTemplate($templateId);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

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
        $request  = $this->createRequestForUpdateTemplate($templateId, $template);
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        return $response;
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
     *
     * @codeCoverageIgnore
     */
    public function createRequestForGetUndeliveredMessages(
        ReferenceRange $range,
        ?DateRange $daterange,
        ?DateRange $receivedrange,
        ?string $sortorder,
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
        ReferenceRange $range,
        ?DateRange $daterange,
        ?DateRange $receivedrange,
        ?string $sortorder,
        ?bool $addevents = false,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): BouncedMessageCollection {
        $request  = $this->createRequestForGetUndeliveredMessages($range, $daterange, $receivedrange, $sortorder, $addevents, $addheaders, $addonlinelink, $addtags);
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
