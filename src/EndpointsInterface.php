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
use Flowmailer\API\Parameter\DateRange;
use Flowmailer\API\Parameter\ItemsRange;
use Flowmailer\API\Parameter\ReferenceRange;
use Flowmailer\API\Serializer\ResponseData;
use Psr\Http\Message\RequestInterface;

interface EndpointsInterface
{
    /**
     * Create the RequestInterface for createOAuthToken.
     *
     * @param $clientId     The API client id provided by Flowmailer
     * @param $clientSecret The API client secret provided by Flowmailer
     * @param $grantType    must be `client_credentials`
     * @param $scope        Must be absent or `api`
     */
    public function createRequestForCreateOAuthToken(
        $clientId,
        $clientSecret,
        $grantType,
        $scope = 'api'
    ): RequestInterface;

    /**
     * This call is used to request an access token using the client id and secret provided by flowmailer.
     *
     * The form parameters must be posted in `application/x-www-form-urlencoded` format. But the response will be in JSON format.
     */
    public function createOAuthToken($clientId, $clientSecret, $grantType, $scope = 'api'): OAuthTokenResponse;

    /**
     * Do the request for createOAuthToken.
     */
    public function doRequestForCreateOAuthToken(RequestInterface $request): OAuthTokenResponse;

    /**
     * Deserialize the responseData for createOAuthToken.
     */
    public function processResponseDataForCreateOAuthToken(ResponseData $response): OAuthTokenResponse;

    /**
     * Create the RequestInterface for createAccount.
     */
    public function createRequestForCreateAccount(Account $account): RequestInterface;

    /**
     * Create an account.
     */
    public function createAccount(Account $account);

    /**
     * Do the request for createAccount.
     */
    public function doRequestForCreateAccount(RequestInterface $request);

    /**
     * Create the RequestInterface for getApiCredentials.
     */
    public function createRequestForGetApiCredentials(?int $sourceId = null): RequestInterface;

    /**
     * Get api credentials.
     */
    public function getApiCredentials(?int $sourceId = null): CredentialsCollection;

    /**
     * Do the request for getApiCredentials.
     */
    public function doRequestForGetApiCredentials(RequestInterface $request): CredentialsCollection;

    /**
     * Deserialize the responseData for getApiCredentials.
     */
    public function processResponseDataForGetApiCredentials(ResponseData $response): CredentialsCollection;

    /**
     * Create the RequestInterface for createApiCredentials.
     */
    public function createRequestForCreateApiCredentials(Credentials $credentials): RequestInterface;

    /**
     * Create api credentials.
     */
    public function createApiCredentials(Credentials $credentials): Credentials;

    /**
     * Do the request for createApiCredentials.
     */
    public function doRequestForCreateApiCredentials(RequestInterface $request): Credentials;

    /**
     * Deserialize the responseData for createApiCredentials.
     */
    public function processResponseDataForCreateApiCredentials(ResponseData $response): Credentials;

    /**
     * Create the RequestInterface for deleteClientApiCredentials.
     */
    public function createRequestForDeleteClientApiCredentials($clientId): RequestInterface;

    /**
     * Delete client api credentials.
     */
    public function deleteClientApiCredentials($clientId);

    /**
     * Do the request for deleteClientApiCredentials.
     */
    public function doRequestForDeleteClientApiCredentials(RequestInterface $request);

    /**
     * Create the RequestInterface for getClientApiCredentials.
     */
    public function createRequestForGetClientApiCredentials($clientId): RequestInterface;

    /**
     * Get client api credentials.
     */
    public function getClientApiCredentials($clientId): Credentials;

    /**
     * Do the request for getClientApiCredentials.
     */
    public function doRequestForGetClientApiCredentials(RequestInterface $request): Credentials;

    /**
     * Deserialize the responseData for getClientApiCredentials.
     */
    public function processResponseDataForGetClientApiCredentials(ResponseData $response): Credentials;

    /**
     * Create the RequestInterface for updateClientApiCredentials.
     */
    public function createRequestForUpdateClientApiCredentials($clientId, Credentials $credentials): RequestInterface;

    /**
     * Update client api credentials.
     */
    public function updateClientApiCredentials($clientId, Credentials $credentials);

    /**
     * Do the request for updateClientApiCredentials.
     */
    public function doRequestForUpdateClientApiCredentials(RequestInterface $request);

    /**
     * Create the RequestInterface for getEventFlowRules.
     */
    public function createRequestForGetEventFlowRules(): RequestInterface;

    /**
     * Get flow rule list for all event flows.
     */
    public function getEventFlowRules(): FlowRuleItemCollection;

    /**
     * Do the request for getEventFlowRules.
     */
    public function doRequestForGetEventFlowRules(RequestInterface $request): FlowRuleItemCollection;

    /**
     * Deserialize the responseData for getEventFlowRules.
     */
    public function processResponseDataForGetEventFlowRules(ResponseData $response): FlowRuleItemCollection;

    /**
     * Create the RequestInterface for getEventFlowRulesHierarchy.
     */
    public function createRequestForGetEventFlowRulesHierarchy(): RequestInterface;

    /**
     * Get flow rule list for all event flows.
     */
    public function getEventFlowRulesHierarchy(): FlowRuleHierarchyItemCollection;

    /**
     * Do the request for getEventFlowRulesHierarchy.
     */
    public function doRequestForGetEventFlowRulesHierarchy(RequestInterface $request): FlowRuleHierarchyItemCollection;

    /**
     * Deserialize the responseData for getEventFlowRulesHierarchy.
     */
    public function processResponseDataForGetEventFlowRulesHierarchy(ResponseData $response): FlowRuleHierarchyItemCollection;

    /**
     * Create the RequestInterface for getEventFlows.
     */
    public function createRequestForGetEventFlows(): RequestInterface;

    /**
     * List flows per account.
     */
    public function getEventFlows(): EventFlowCollection;

    /**
     * Do the request for getEventFlows.
     */
    public function doRequestForGetEventFlows(RequestInterface $request): EventFlowCollection;

    /**
     * Deserialize the responseData for getEventFlows.
     */
    public function processResponseDataForGetEventFlows(ResponseData $response): EventFlowCollection;

    /**
     * Create the RequestInterface for createEventFlow.
     *
     * @param EventFlow $eventFlow Flow object
     */
    public function createRequestForCreateEventFlow(EventFlow $eventFlow): RequestInterface;

    /**
     * Create a new flow.
     */
    public function createEventFlow(EventFlow $eventFlow);

    /**
     * Do the request for createEventFlow.
     */
    public function doRequestForCreateEventFlow(RequestInterface $request);

    /**
     * Create the RequestInterface for deleteEventFlow.
     *
     * @param $eventFlowId Flow ID
     */
    public function createRequestForDeleteEventFlow($eventFlowId): RequestInterface;

    /**
     * Delete flow by id.
     */
    public function deleteEventFlow($eventFlowId);

    /**
     * Do the request for deleteEventFlow.
     */
    public function doRequestForDeleteEventFlow(RequestInterface $request);

    /**
     * Create the RequestInterface for getEventFlow.
     *
     * @param $eventFlowId Flow ID
     */
    public function createRequestForGetEventFlow($eventFlowId): RequestInterface;

    /**
     * Get flow by id.
     */
    public function getEventFlow($eventFlowId): EventFlow;

    /**
     * Do the request for getEventFlow.
     */
    public function doRequestForGetEventFlow(RequestInterface $request): EventFlow;

    /**
     * Deserialize the responseData for getEventFlow.
     */
    public function processResponseDataForGetEventFlow(ResponseData $response): EventFlow;

    /**
     * Create the RequestInterface for updateEventFlow.
     *
     * @param           $eventFlowId Flow ID
     * @param EventFlow $eventFlow   Flow object
     */
    public function createRequestForUpdateEventFlow($eventFlowId, EventFlow $eventFlow): RequestInterface;

    /**
     * Save flow.
     */
    public function updateEventFlow($eventFlowId, EventFlow $eventFlow): EventFlow;

    /**
     * Do the request for updateEventFlow.
     */
    public function doRequestForUpdateEventFlow(RequestInterface $request): EventFlow;

    /**
     * Deserialize the responseData for updateEventFlow.
     */
    public function processResponseDataForUpdateEventFlow(ResponseData $response): EventFlow;

    /**
     * Create the RequestInterface for getEventFlowRule.
     *
     * @param $eventFlowId Flow ID
     */
    public function createRequestForGetEventFlowRule($eventFlowId): RequestInterface;

    /**
     * Get flow conditions for a flow.
     */
    public function getEventFlowRule($eventFlowId): EventFlowRuleSimple;

    /**
     * Do the request for getEventFlowRule.
     */
    public function doRequestForGetEventFlowRule(RequestInterface $request): EventFlowRuleSimple;

    /**
     * Deserialize the responseData for getEventFlowRule.
     */
    public function processResponseDataForGetEventFlowRule(ResponseData $response): EventFlowRuleSimple;

    /**
     * Create the RequestInterface for updateEventFlowRule.
     *
     * @param                     $eventFlowId         Flow ID
     * @param EventFlowRuleSimple $eventFlowRuleSimple Flow conditions
     */
    public function createRequestForUpdateEventFlowRule(
        $eventFlowId,
        EventFlowRuleSimple $eventFlowRuleSimple
    ): RequestInterface;

    /**
     * Set conditions for a flow.
     */
    public function updateEventFlowRule($eventFlowId, EventFlowRuleSimple $eventFlowRuleSimple);

    /**
     * Do the request for updateEventFlowRule.
     */
    public function doRequestForUpdateEventFlowRule(RequestInterface $request);

    /**
     * Create the RequestInterface for getFilters.
     *
     * @param ReferenceRange $range     Limits the returned list
     * @param DateRange      $daterange Date range the filter was added in
     */
    public function createRequestForGetFilters(
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null
    ): RequestInterface;

    /**
     * List filters per account.
     */
    public function getFilters(
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null
    ): FilterCollection;

    /**
     * Do the request for getFilters.
     */
    public function doRequestForGetFilters(RequestInterface $request): FilterCollection;

    /**
     * Deserialize the responseData for getFilters.
     */
    public function processResponseDataForGetFilters(ResponseData $response): FilterCollection;

    /**
     * Create the RequestInterface for deleteFilter.
     *
     * @param $filterId Filter ID
     */
    public function createRequestForDeleteFilter($filterId): RequestInterface;

    /**
     * Delete a recipient from the filter.
     */
    public function deleteFilter($filterId);

    /**
     * Do the request for deleteFilter.
     */
    public function doRequestForDeleteFilter(RequestInterface $request);

    /**
     * Create the RequestInterface for getFlowRules.
     */
    public function createRequestForGetFlowRules(): RequestInterface;

    /**
     * Get flow rule list for all flows.
     */
    public function getFlowRules(): FlowRuleItemCollection;

    /**
     * Do the request for getFlowRules.
     */
    public function doRequestForGetFlowRules(RequestInterface $request): FlowRuleItemCollection;

    /**
     * Deserialize the responseData for getFlowRules.
     */
    public function processResponseDataForGetFlowRules(ResponseData $response): FlowRuleItemCollection;

    /**
     * Create the RequestInterface for getFlowTemplates.
     */
    public function createRequestForGetFlowTemplates(): RequestInterface;

    /**
     * List flow templates per account.
     */
    public function getFlowTemplates(): FlowTemplateCollection;

    /**
     * Do the request for getFlowTemplates.
     */
    public function doRequestForGetFlowTemplates(RequestInterface $request): FlowTemplateCollection;

    /**
     * Deserialize the responseData for getFlowTemplates.
     */
    public function processResponseDataForGetFlowTemplates(ResponseData $response): FlowTemplateCollection;

    /**
     * Create the RequestInterface for getFlows.
     *
     * @param bool $statistics Whether to return statistics per flow
     */
    public function createRequestForGetFlows(?bool $statistics = true): RequestInterface;

    /**
     * List flows per account.
     */
    public function getFlows(?bool $statistics = true): FlowCollection;

    /**
     * Do the request for getFlows.
     */
    public function doRequestForGetFlows(RequestInterface $request): FlowCollection;

    /**
     * Deserialize the responseData for getFlows.
     */
    public function processResponseDataForGetFlows(ResponseData $response): FlowCollection;

    /**
     * Create the RequestInterface for createFlow.
     *
     * @param Flow $flow Flow object
     */
    public function createRequestForCreateFlow(Flow $flow): RequestInterface;

    /**
     * Create a new flow.
     */
    public function createFlow(Flow $flow);

    /**
     * Do the request for createFlow.
     */
    public function doRequestForCreateFlow(RequestInterface $request);

    /**
     * Create the RequestInterface for deleteFlow.
     *
     * @param $flowId Flow ID
     */
    public function createRequestForDeleteFlow($flowId): RequestInterface;

    /**
     * Delete flow by id.
     */
    public function deleteFlow($flowId);

    /**
     * Do the request for deleteFlow.
     */
    public function doRequestForDeleteFlow(RequestInterface $request);

    /**
     * Create the RequestInterface for getFlow.
     *
     * @param $flowId Flow ID
     */
    public function createRequestForGetFlow($flowId): RequestInterface;

    /**
     * Get flow by id.
     */
    public function getFlow($flowId): Flow;

    /**
     * Do the request for getFlow.
     */
    public function doRequestForGetFlow(RequestInterface $request): Flow;

    /**
     * Deserialize the responseData for getFlow.
     */
    public function processResponseDataForGetFlow(ResponseData $response): Flow;

    /**
     * Create the RequestInterface for updateFlow.
     *
     * @param      $flowId Flow ID
     * @param Flow $flow   Flow object
     */
    public function createRequestForUpdateFlow($flowId, Flow $flow): RequestInterface;

    /**
     * Save flow.
     */
    public function updateFlow($flowId, Flow $flow): Flow;

    /**
     * Do the request for updateFlow.
     */
    public function doRequestForUpdateFlow(RequestInterface $request): Flow;

    /**
     * Deserialize the responseData for updateFlow.
     */
    public function processResponseDataForUpdateFlow(ResponseData $response): Flow;

    /**
     * Create the RequestInterface for getFlowMessages.
     *
     * @param            $flowId     Flow ID
     * @param DateRange  $daterange  Date range the message was submitted in
     * @param ItemsRange $range      Limits the returned list
     * @param bool       $addheaders Whether to add e-mail headers
     */
    public function createRequestForGetFlowMessages(
        $flowId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface;

    /**
     * List messages per flow.
     */
    public function getFlowMessages(
        $flowId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection;

    /**
     * Do the request for getFlowMessages.
     */
    public function doRequestForGetFlowMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getFlowMessages.
     */
    public function processResponseDataForGetFlowMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for getFlowRule.
     *
     * @param $flowId Flow ID
     */
    public function createRequestForGetFlowRule($flowId): RequestInterface;

    /**
     * Get flow conditions for a flow.
     */
    public function getFlowRule($flowId): FlowRuleSimple;

    /**
     * Do the request for getFlowRule.
     */
    public function doRequestForGetFlowRule(RequestInterface $request): FlowRuleSimple;

    /**
     * Deserialize the responseData for getFlowRule.
     */
    public function processResponseDataForGetFlowRule(ResponseData $response): FlowRuleSimple;

    /**
     * Create the RequestInterface for updateFlowRule.
     *
     * @param                $flowId         Flow ID
     * @param FlowRuleSimple $flowRuleSimple Flow conditions
     */
    public function createRequestForUpdateFlowRule($flowId, FlowRuleSimple $flowRuleSimple): RequestInterface;

    /**
     * Set conditions for a flow.
     */
    public function updateFlowRule($flowId, FlowRuleSimple $flowRuleSimple);

    /**
     * Do the request for updateFlowRule.
     */
    public function doRequestForUpdateFlowRule(RequestInterface $request);

    /**
     * Create the RequestInterface for getFlowStats.
     *
     * @param           $flowId    Flow ID
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     */
    public function createRequestForGetFlowStats($flowId, DateRange $daterange, ?int $interval = null): RequestInterface;

    /**
     * Get time based message statistics for a message flow.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     */
    public function getFlowStats($flowId, DateRange $daterange, ?int $interval = null): DataSets;

    /**
     * Do the request for getFlowStats.
     */
    public function doRequestForGetFlowStats(RequestInterface $request): DataSets;

    /**
     * Deserialize the responseData for getFlowStats.
     */
    public function processResponseDataForGetFlowStats(ResponseData $response): DataSets;

    /**
     * Create the RequestInterface for getMessageEvents.
     *
     * @param ReferenceRange $range          Limits the returned list
     * @param array          $flowIds        Filter results on message flow ID
     * @param array          $sourceIds      Filter results on message source ID
     * @param bool           $addmessagetags Message tags will be included with each event if this parameter is true
     */
    public function createRequestForGetMessageEvents(
        ReferenceRange $range,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?string $sortorder = null,
        ?bool $addmessagetags = false,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null
    ): RequestInterface;

    /**
     * List message events.
     *
     *  Ordered by received, new events first.
     */
    public function getMessageEvents(
        ReferenceRange $range,
        ?array $flowIds = null,
        ?array $sourceIds = null,
        ?string $sortorder = null,
        ?bool $addmessagetags = false,
        ?DateRange $daterange = null,
        ?DateRange $receivedrange = null
    ): MessageEventCollection;

    /**
     * Do the request for getMessageEvents.
     */
    public function doRequestForGetMessageEvents(RequestInterface $request): MessageEventCollection;

    /**
     * Deserialize the responseData for getMessageEvents.
     */
    public function processResponseDataForGetMessageEvents(ResponseData $response): MessageEventCollection;

    /**
     * Create the RequestInterface for getMessageHolds.
     *
     * @param ItemsRange $range     Limits the returned list
     * @param DateRange  $daterange Date range the message was submitted in
     */
    public function createRequestForGetMessageHolds(ItemsRange $range, ?DateRange $daterange = null): RequestInterface;

    /**
     * List messages which could not be processed.
     */
    public function getMessageHolds(ItemsRange $range, ?DateRange $daterange = null): MessageHoldCollection;

    /**
     * Do the request for getMessageHolds.
     */
    public function doRequestForGetMessageHolds(RequestInterface $request): MessageHoldCollection;

    /**
     * Deserialize the responseData for getMessageHolds.
     */
    public function processResponseDataForGetMessageHolds(ResponseData $response): MessageHoldCollection;

    /**
     * Create the RequestInterface for getMessageHold.
     *
     * @param $messageId Message ID
     */
    public function createRequestForGetMessageHold($messageId): RequestInterface;

    /**
     * Get a held message by its id.
     */
    public function getMessageHold($messageId): MessageHold;

    /**
     * Do the request for getMessageHold.
     */
    public function doRequestForGetMessageHold(RequestInterface $request): MessageHold;

    /**
     * Deserialize the responseData for getMessageHold.
     */
    public function processResponseDataForGetMessageHold(ResponseData $response): MessageHold;

    /**
     * Create the RequestInterface for getMessages.
     *
     * @param ReferenceRange $range      Limits the returned list
     * @param array          $flowIds    Filter results on flow ID
     * @param string         $sortfield  Sort by INSERTED or SUBMITTED (default INSERTED)
     * @param bool           $addevents  Whether to add message events
     * @param bool           $addheaders Whether to add e-mail headers
     * @param DateRange      $daterange  Date range the message was submitted in
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
    ): RequestInterface;

    /**
     * List messages.
     *
     *  This API call can be used to retrieve all messages and keep your database up to date (without missing messages due to paging issues). To do this sortfield must be set to INSERTED, and sortorder should be set to ASC.
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
    ): MessageCollection;

    /**
     * Do the request for getMessages.
     */
    public function doRequestForGetMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getMessages.
     */
    public function processResponseDataForGetMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for simulateMessage.
     */
    public function createRequestForSimulateMessage(SimulateMessage $simulateMessage): RequestInterface;

    /**
     * Simulate an email or sms message.
     */
    public function simulateMessage(SimulateMessage $simulateMessage): SimulateMessageResult;

    /**
     * Do the request for simulateMessage.
     */
    public function doRequestForSimulateMessage(RequestInterface $request): SimulateMessageResult;

    /**
     * Deserialize the responseData for simulateMessage.
     */
    public function processResponseDataForSimulateMessage(ResponseData $response): SimulateMessageResult;

    /**
     * Create the RequestInterface for submitMessage.
     */
    public function createRequestForSubmitMessage(SubmitMessage $submitMessage): RequestInterface;

    /**
     * Send an email or sms message.
     */
    public function submitMessage(SubmitMessage $submitMessage);

    /**
     * Do the request for submitMessage.
     */
    public function doRequestForSubmitMessage(RequestInterface $request);

    /**
     * Create the RequestInterface for getMessage.
     *
     * @param $messageId Message ID
     */
    public function createRequestForGetMessage($messageId, ?bool $addtags = false): RequestInterface;

    /**
     * Get message by id.
     */
    public function getMessage($messageId, ?bool $addtags = false): Message;

    /**
     * Do the request for getMessage.
     */
    public function doRequestForGetMessage(RequestInterface $request): Message;

    /**
     * Deserialize the responseData for getMessage.
     */
    public function processResponseDataForGetMessage(ResponseData $response): Message;

    /**
     * Create the RequestInterface for getMessageArchive.
     *
     * @param $messageId Message ID
     */
    public function createRequestForGetMessageArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): RequestInterface;

    /**
     * List the message as archived by one or more flow steps.
     */
    public function getMessageArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): MessageArchiveCollection;

    /**
     * Do the request for getMessageArchive.
     */
    public function doRequestForGetMessageArchive(RequestInterface $request): MessageArchiveCollection;

    /**
     * Deserialize the responseData for getMessageArchive.
     */
    public function processResponseDataForGetMessageArchive(ResponseData $response): MessageArchiveCollection;

    /**
     * Create the RequestInterface for getMessageArchiveAttachment.
     *
     * @param $messageId  Message ID
     * @param $flowStepId Flow step ID
     * @param $contentId  Attachment content ID
     */
    public function createRequestForGetMessageArchiveAttachment($messageId, $flowStepId, $contentId): RequestInterface;

    /**
     * Fetch an attachment including data for an archived message.
     */
    public function getMessageArchiveAttachment($messageId, $flowStepId, $contentId): Attachment;

    /**
     * Do the request for getMessageArchiveAttachment.
     */
    public function doRequestForGetMessageArchiveAttachment(RequestInterface $request): Attachment;

    /**
     * Deserialize the responseData for getMessageArchiveAttachment.
     */
    public function processResponseDataForGetMessageArchiveAttachment(ResponseData $response): Attachment;

    /**
     * Create the RequestInterface for getMessageErrorArchive.
     */
    public function createRequestForGetMessageErrorArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): RequestInterface;

    /**
     * Get message error archive.
     */
    public function getMessageErrorArchive(
        $messageId,
        ?bool $addattachments = false,
        ?bool $adddata = false
    ): MessageArchive;

    /**
     * Do the request for getMessageErrorArchive.
     */
    public function doRequestForGetMessageErrorArchive(RequestInterface $request): MessageArchive;

    /**
     * Deserialize the responseData for getMessageErrorArchive.
     */
    public function processResponseDataForGetMessageErrorArchive(ResponseData $response): MessageArchive;

    /**
     * Create the RequestInterface for resendMessage.
     *
     * @param $messageId Message ID
     */
    public function createRequestForResendMessage($messageId, ResendMessage $resendMessage): RequestInterface;

    /**
     * Resend message by id.
     */
    public function resendMessage($messageId, ResendMessage $resendMessage);

    /**
     * Do the request for resendMessage.
     */
    public function doRequestForResendMessage(RequestInterface $request);

    /**
     * Create the RequestInterface for getMessageStats.
     *
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     */
    public function createRequestForGetMessageStats(
        DateRange $daterange,
        ?array $flowIds = null,
        ?int $interval = null
    ): RequestInterface;

    /**
     * Get time based message statistics for whole account.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     */
    public function getMessageStats(DateRange $daterange, ?array $flowIds = null, ?int $interval = null): DataSets;

    /**
     * Do the request for getMessageStats.
     */
    public function doRequestForGetMessageStats(RequestInterface $request): DataSets;

    /**
     * Deserialize the responseData for getMessageStats.
     */
    public function processResponseDataForGetMessageStats(ResponseData $response): DataSets;

    /**
     * Create the RequestInterface for getRecipient.
     *
     * @param           $recipient Recipient email address or phone number
     * @param DateRange $daterange Specifies the date range for message statistics
     */
    public function createRequestForGetRecipient($recipient, ?DateRange $daterange = null): RequestInterface;

    /**
     * Get information about a recipient.
     *
     *  Message statistics are only included if a date range is specified.
     */
    public function getRecipient($recipient, ?DateRange $daterange = null): Recipient;

    /**
     * Do the request for getRecipient.
     */
    public function doRequestForGetRecipient(RequestInterface $request): Recipient;

    /**
     * Deserialize the responseData for getRecipient.
     */
    public function processResponseDataForGetRecipient(ResponseData $response): Recipient;

    /**
     * Create the RequestInterface for getRecipientMessages.
     *
     * @param                $recipient     Recipient email address or phone number
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     */
    public function createRequestForGetRecipientMessages(
        $recipient,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface;

    /**
     * List messages per recipient.
     */
    public function getRecipientMessages(
        $recipient,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection;

    /**
     * Do the request for getRecipientMessages.
     */
    public function doRequestForGetRecipientMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getRecipientMessages.
     */
    public function processResponseDataForGetRecipientMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for getRoles.
     */
    public function createRequestForGetRoles(): RequestInterface;

    /**
     * Get roles.
     */
    public function getRoles(): RoleCollection;

    /**
     * Do the request for getRoles.
     */
    public function doRequestForGetRoles(RequestInterface $request): RoleCollection;

    /**
     * Deserialize the responseData for getRoles.
     */
    public function processResponseDataForGetRoles(ResponseData $response): RoleCollection;

    /**
     * Create the RequestInterface for getSenderMessages.
     *
     * @param                $sender        Sender email address or phone number
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     */
    public function createRequestForGetSenderMessages(
        $sender,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface;

    /**
     * List messages per sender.
     */
    public function getSenderMessages(
        $sender,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection;

    /**
     * Do the request for getSenderMessages.
     */
    public function doRequestForGetSenderMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getSenderMessages.
     */
    public function processResponseDataForGetSenderMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for getSenderDomains.
     */
    public function createRequestForGetSenderDomains(): RequestInterface;

    /**
     * List sender domains by account.
     */
    public function getSenderDomains(): SenderDomainCollection;

    /**
     * Do the request for getSenderDomains.
     */
    public function doRequestForGetSenderDomains(RequestInterface $request): SenderDomainCollection;

    /**
     * Deserialize the responseData for getSenderDomains.
     */
    public function processResponseDataForGetSenderDomains(ResponseData $response): SenderDomainCollection;

    /**
     * Create the RequestInterface for createSenderDomain.
     */
    public function createRequestForCreateSenderDomain(SenderDomain $senderDomain): RequestInterface;

    /**
     * Create sender domain.
     */
    public function createSenderDomain(SenderDomain $senderDomain);

    /**
     * Do the request for createSenderDomain.
     */
    public function doRequestForCreateSenderDomain(RequestInterface $request);

    /**
     * Create the RequestInterface for getSenderDomainsByDomain.
     *
     * @param      $domain   Sender domain name
     * @param bool $validate Validate DNS records for this SenderDomain
     */
    public function createRequestForGetSenderDomainsByDomain($domain, ?bool $validate = false): RequestInterface;

    /**
     * Get sender domain by domain name.
     */
    public function getSenderDomainsByDomain($domain, ?bool $validate = false): SenderDomain;

    /**
     * Do the request for getSenderDomainsByDomain.
     */
    public function doRequestForGetSenderDomainsByDomain(RequestInterface $request): SenderDomain;

    /**
     * Deserialize the responseData for getSenderDomainsByDomain.
     */
    public function processResponseDataForGetSenderDomainsByDomain(ResponseData $response): SenderDomain;

    /**
     * Create the RequestInterface for validateSenderDomain.
     *
     * @param SenderDomain $senderDomain the sender domain to validate
     */
    public function createRequestForValidateSenderDomain(SenderDomain $senderDomain): RequestInterface;

    /**
     * Validates but does not save a sender domain.
     */
    public function validateSenderDomain(SenderDomain $senderDomain): SenderDomain;

    /**
     * Do the request for validateSenderDomain.
     */
    public function doRequestForValidateSenderDomain(RequestInterface $request): SenderDomain;

    /**
     * Deserialize the responseData for validateSenderDomain.
     */
    public function processResponseDataForValidateSenderDomain(ResponseData $response): SenderDomain;

    /**
     * Create the RequestInterface for deleteSenderDomain.
     *
     * @param $domainId Sender domain ID
     */
    public function createRequestForDeleteSenderDomain($domainId): RequestInterface;

    /**
     * Delete sender domain.
     */
    public function deleteSenderDomain($domainId);

    /**
     * Do the request for deleteSenderDomain.
     */
    public function doRequestForDeleteSenderDomain(RequestInterface $request);

    /**
     * Create the RequestInterface for getSenderDomain.
     *
     * @param      $domainId Sender domain ID
     * @param bool $validate Validate DNS records for this SenderDomain
     */
    public function createRequestForGetSenderDomain($domainId, ?bool $validate = false): RequestInterface;

    /**
     * Get sender domain by id.
     */
    public function getSenderDomain($domainId, ?bool $validate = false): SenderDomain;

    /**
     * Do the request for getSenderDomain.
     */
    public function doRequestForGetSenderDomain(RequestInterface $request): SenderDomain;

    /**
     * Deserialize the responseData for getSenderDomain.
     */
    public function processResponseDataForGetSenderDomain(ResponseData $response): SenderDomain;

    /**
     * Create the RequestInterface for updateSenderDomain.
     *
     * @param $domainId Sender domain ID
     */
    public function createRequestForUpdateSenderDomain($domainId, SenderDomain $senderDomain): RequestInterface;

    /**
     * Save sender domain.
     */
    public function updateSenderDomain($domainId, SenderDomain $senderDomain);

    /**
     * Do the request for updateSenderDomain.
     */
    public function doRequestForUpdateSenderDomain(RequestInterface $request);

    /**
     * Create the RequestInterface for getSources.
     *
     * @param bool $statistics Whether to include message statistics or not
     */
    public function createRequestForGetSources(?bool $statistics = true): RequestInterface;

    /**
     * List source systems per account.
     */
    public function getSources(?bool $statistics = true): SourceCollection;

    /**
     * Do the request for getSources.
     */
    public function doRequestForGetSources(RequestInterface $request): SourceCollection;

    /**
     * Deserialize the responseData for getSources.
     */
    public function processResponseDataForGetSources(ResponseData $response): SourceCollection;

    /**
     * Create the RequestInterface for createSource.
     */
    public function createRequestForCreateSource(Source $source): RequestInterface;

    /**
     * Create a new source.
     */
    public function createSource(Source $source);

    /**
     * Do the request for createSource.
     */
    public function doRequestForCreateSource(RequestInterface $request);

    /**
     * Create the RequestInterface for deleteSource.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForDeleteSource($sourceId): RequestInterface;

    /**
     * Delete a source.
     */
    public function deleteSource($sourceId);

    /**
     * Do the request for deleteSource.
     */
    public function doRequestForDeleteSource(RequestInterface $request);

    /**
     * Create the RequestInterface for getSource.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForGetSource($sourceId): RequestInterface;

    /**
     * Get a source by id.
     */
    public function getSource($sourceId): Source;

    /**
     * Do the request for getSource.
     */
    public function doRequestForGetSource(RequestInterface $request): Source;

    /**
     * Deserialize the responseData for getSource.
     */
    public function processResponseDataForGetSource(ResponseData $response): Source;

    /**
     * Create the RequestInterface for updateSource.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForUpdateSource($sourceId, Source $source): RequestInterface;

    /**
     * Update a source.
     */
    public function updateSource($sourceId, Source $source);

    /**
     * Do the request for updateSource.
     */
    public function doRequestForUpdateSource(RequestInterface $request);

    /**
     * Create the RequestInterface for getSourceMessages.
     *
     * @param            $sourceId   Source ID
     * @param DateRange  $daterange  Date range the message was submitted in
     * @param ItemsRange $range      Limits the returned list
     * @param bool       $addheaders Whether to add e-mail headers
     */
    public function createRequestForGetSourceMessages(
        $sourceId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface;

    /**
     * List messages per source.
     */
    public function getSourceMessages(
        $sourceId,
        DateRange $daterange,
        ItemsRange $range,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection;

    /**
     * Do the request for getSourceMessages.
     */
    public function doRequestForGetSourceMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getSourceMessages.
     */
    public function processResponseDataForGetSourceMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for getSourceStats.
     *
     * @param           $sourceId  Source ID
     * @param DateRange $daterange Date range the messages were submitted in
     * @param int       $interval  Time difference between samples
     */
    public function createRequestForGetSourceStats(
        $sourceId,
        DateRange $daterange,
        ?int $interval = null
    ): RequestInterface;

    /**
     * Get time based message statistics for a message source.
     *
     *  The resolution of the returned data may be lower than specified in the `interval` parameter if the data is old or the requested date range is too large.
     */
    public function getSourceStats($sourceId, DateRange $daterange, ?int $interval = null): DataSets;

    /**
     * Do the request for getSourceStats.
     */
    public function doRequestForGetSourceStats(RequestInterface $request): DataSets;

    /**
     * Deserialize the responseData for getSourceStats.
     */
    public function processResponseDataForGetSourceStats(ResponseData $response): DataSets;

    /**
     * Create the RequestInterface for getSourceUsers.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForGetSourceUsers($sourceId): RequestInterface;

    /**
     * List credentials per source system.
     */
    public function getSourceUsers($sourceId): CredentialsCollection;

    /**
     * Do the request for getSourceUsers.
     */
    public function doRequestForGetSourceUsers(RequestInterface $request): CredentialsCollection;

    /**
     * Deserialize the responseData for getSourceUsers.
     */
    public function processResponseDataForGetSourceUsers(ResponseData $response): CredentialsCollection;

    /**
     * Create the RequestInterface for createSourceUsers.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForCreateSourceUsers($sourceId, Credentials $credentials): RequestInterface;

    /**
     * Create credentials for a source.
     */
    public function createSourceUsers($sourceId, Credentials $credentials): Credentials;

    /**
     * Do the request for createSourceUsers.
     */
    public function doRequestForCreateSourceUsers(RequestInterface $request): Credentials;

    /**
     * Deserialize the responseData for createSourceUsers.
     */
    public function processResponseDataForCreateSourceUsers(ResponseData $response): Credentials;

    /**
     * Create the RequestInterface for deleteSourceUser.
     *
     * @param $sourceId Source ID
     * @param $userId   Credential ID
     */
    public function createRequestForDeleteSourceUser($sourceId, $userId): RequestInterface;

    /**
     * Delete credentials.
     */
    public function deleteSourceUser($sourceId, $userId);

    /**
     * Do the request for deleteSourceUser.
     */
    public function doRequestForDeleteSourceUser(RequestInterface $request);

    /**
     * Create the RequestInterface for getSourceUser.
     *
     * @param $sourceId Source ID
     */
    public function createRequestForGetSourceUser($sourceId, $userId): RequestInterface;

    /**
     * Get credentials for a source.
     */
    public function getSourceUser($sourceId, $userId): Credentials;

    /**
     * Do the request for getSourceUser.
     */
    public function doRequestForGetSourceUser(RequestInterface $request): Credentials;

    /**
     * Deserialize the responseData for getSourceUser.
     */
    public function processResponseDataForGetSourceUser(ResponseData $response): Credentials;

    /**
     * Create the RequestInterface for updateSourceUser.
     *
     * @param $sourceId Source ID
     * @param $userId   Credential ID
     */
    public function createRequestForUpdateSourceUser($sourceId, $userId, Credentials $credentials): RequestInterface;

    /**
     * Update credentials for a source.
     */
    public function updateSourceUser($sourceId, $userId, Credentials $credentials): Credentials;

    /**
     * Do the request for updateSourceUser.
     */
    public function doRequestForUpdateSourceUser(RequestInterface $request): Credentials;

    /**
     * Deserialize the responseData for updateSourceUser.
     */
    public function processResponseDataForUpdateSourceUser(ResponseData $response): Credentials;

    /**
     * Create the RequestInterface for getTagMessages.
     *
     * @param                $tag           Tag
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the messages were submitted in
     * @param bool           $addheaders    Whether to add e-mail headers
     * @param bool           $addonlinelink Whether to add online link
     */
    public function createRequestForGetTagMessages(
        $tag,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): RequestInterface;

    /**
     * List messages per tag.
     */
    public function getTagMessages(
        $tag,
        ReferenceRange $range,
        ?DateRange $daterange = null,
        ?string $sortorder = null,
        ?bool $addheaders = false,
        ?bool $addonlinelink = false,
        ?bool $addtags = false
    ): MessageCollection;

    /**
     * Do the request for getTagMessages.
     */
    public function doRequestForGetTagMessages(RequestInterface $request): MessageCollection;

    /**
     * Deserialize the responseData for getTagMessages.
     */
    public function processResponseDataForGetTagMessages(ResponseData $response): MessageCollection;

    /**
     * Create the RequestInterface for getTemplates.
     */
    public function createRequestForGetTemplates(): RequestInterface;

    /**
     * List templates by account.
     */
    public function getTemplates(): TemplateCollection;

    /**
     * Do the request for getTemplates.
     */
    public function doRequestForGetTemplates(RequestInterface $request): TemplateCollection;

    /**
     * Deserialize the responseData for getTemplates.
     */
    public function processResponseDataForGetTemplates(ResponseData $response): TemplateCollection;

    /**
     * Create the RequestInterface for createTemplate.
     *
     * @param Template $template Template object
     */
    public function createRequestForCreateTemplate(Template $template): RequestInterface;

    /**
     * Create template.
     */
    public function createTemplate(Template $template);

    /**
     * Do the request for createTemplate.
     */
    public function doRequestForCreateTemplate(RequestInterface $request);

    /**
     * Create the RequestInterface for deleteTemplate.
     *
     * @param $templateId Template ID
     */
    public function createRequestForDeleteTemplate($templateId): RequestInterface;

    /**
     * Delete template by id.
     */
    public function deleteTemplate($templateId);

    /**
     * Do the request for deleteTemplate.
     */
    public function doRequestForDeleteTemplate(RequestInterface $request);

    /**
     * Create the RequestInterface for getTemplate.
     *
     * @param $templateId Template ID
     */
    public function createRequestForGetTemplate($templateId): RequestInterface;

    /**
     * Get template by id.
     */
    public function getTemplate($templateId): Template;

    /**
     * Do the request for getTemplate.
     */
    public function doRequestForGetTemplate(RequestInterface $request): Template;

    /**
     * Deserialize the responseData for getTemplate.
     */
    public function processResponseDataForGetTemplate(ResponseData $response): Template;

    /**
     * Create the RequestInterface for updateTemplate.
     *
     * @param          $templateId Template ID
     * @param Template $template   Template object
     */
    public function createRequestForUpdateTemplate($templateId, Template $template): RequestInterface;

    /**
     * Save template.
     */
    public function updateTemplate($templateId, Template $template);

    /**
     * Do the request for updateTemplate.
     */
    public function doRequestForUpdateTemplate(RequestInterface $request);

    /**
     * Create the RequestInterface for getUndeliveredMessages.
     *
     * @param ReferenceRange $range         Limits the returned list
     * @param DateRange      $daterange     Date range the message was submitted in
     * @param DateRange      $receivedrange Date range the message bounced
     * @param bool           $addevents     Whether to add message events
     * @param bool           $addheaders    Whether to add e-mail headers
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
    ): RequestInterface;

    /**
     * List undeliverable messages.
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
    ): BouncedMessageCollection;

    /**
     * Do the request for getUndeliveredMessages.
     */
    public function doRequestForGetUndeliveredMessages(RequestInterface $request): BouncedMessageCollection;

    /**
     * Deserialize the responseData for getUndeliveredMessages.
     */
    public function processResponseDataForGetUndeliveredMessages(ResponseData $response): BouncedMessageCollection;

    /**
     * Create the RequestInterface for getUsers.
     */
    public function createRequestForGetUsers(): RequestInterface;

    /**
     * Get users.
     */
    public function getUsers(): AccountUserCollection;

    /**
     * Do the request for getUsers.
     */
    public function doRequestForGetUsers(RequestInterface $request): AccountUserCollection;

    /**
     * Deserialize the responseData for getUsers.
     */
    public function processResponseDataForGetUsers(ResponseData $response): AccountUserCollection;

    /**
     * Create the RequestInterface for addUser.
     */
    public function createRequestForAddUser(AccountUser $accountUser): RequestInterface;

    /**
     * Create a user.
     */
    public function addUser(AccountUser $accountUser);

    /**
     * Do the request for addUser.
     */
    public function doRequestForAddUser(RequestInterface $request);
}
