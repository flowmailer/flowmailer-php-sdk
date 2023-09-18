<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Model;

use Flowmailer\API\Enum\FlowStepType;

/**
 * FlowStep.
 *
 * A processing step in a flow
 */
class FlowStep implements ModelInterface
{
    /**
     * Only applicable and required when `type` = `addAttachment`.
     */
    private ?FlowStepAddAttachment $addAttachment = null;

    /**
     * Only applicable when `type` = `aggregate`.
     */
    private ?FlowStepAggregate $aggregate = null;

    /**
     * Comma separated list of link domains to modify for analytics parameters.
     *
     *  Only applicable when `type` = `analytics`
     */
    private ?string $applyToLinkDomains = null;

    /**
     * Only applicable and required when `type` = `archive`.
     */
    private ?FlowStepArchive $archive = null;

    /**
     * Only applicable and required when `type` = `discard`.
     */
    private ?FlowStepDiscard $discard = null;

    /**
     * Only applicable when `type` = `qamail`.
     */
    private ?int $divisor = null;

    /**
     * Only applicable and required when `type` = `dnsLookup`.
     */
    private ?FlowStepDnsLookup $dnsLookup = null;

    private ?string $enabledExpression = null;

    /**
     * Indicates whether the contact is required or not.
     *
     *  Only applicable when `type` = `mailPlusContact`
     */
    private ?bool $errorOnNotFound = null;

    /**
     * Only applicable and required when `type` = `externalContent`.
     */
    private ?FlowStepExternalContent $externalContent = null;

    /**
     * Only applicable and required when `type` = `externalData`.
     */
    private ?FlowStepExternalData $externalData = null;

    /**
     * Only applicable and required when `type` = `extractdata`.
     */
    private ?FlowStepExtractData $extractData = null;

    /**
     * Flow step ID.
     */
    private ?string $id = null;

    /**
     * Only applicable and required when `type` = `ldapSearch`.
     */
    private ?FlowStepLdapSearch $ldapSearch = null;

    /**
     * Credentials to use for retrieving contacts from MailPlus.
     *
     *  Only applicable when `type` = `mailPlusContact`
     */
    private ?MailPlusAPICredentials $mailPlusApiCredentials = null;

    /**
     * Overwrite existing URL Parameters in links.
     *
     *  Only applicable when `type` = `analytics`
     */
    private ?bool $overwriteUrlParameters = null;

    /**
     * Only applicable when `type` = `resubmitMessage`.
     */
    private ?FlowStepResubmitMessage $resubmitMessage = null;

    /**
     * Only applicable and required when `type` = `rewriteRecipient`.
     */
    private ?FlowStepRewriteRecipient $rewriteRecipient = null;

    /**
     * Only applicable when `type` = `schedule`.
     */
    private ?FlowStepSchedule $schedule = null;

    /**
     * Only applicable and required when `type` = `addHeader`.
     */
    private ?FlowStepSetHeader $setHeader = null;

    /**
     * Only applicable and required when `type` = `setSender`.
     */
    private ?FlowStepSetSender $setSender = null;

    /**
     * Template for the new subject. Template variables can be used in this field.
     *
     *  Only applicable when `type` = `subject`
     */
    private ?string $subjectTemplate = null;

    /**
     * Only applicable when `type` = `template`.
     */
    private ?ObjectDescription $template = null;

    /**
     * Email address the BCC mail will be sent to.
     *
     *  Only applicable and required when `type` = `qamail`
     */
    private ?string $to = null;

    /**
     * Flow step type.
     */
    private string|FlowStepType $type;

    /**
     * URL Parameters to add to all links. Template variables can be used in this field.
     *
     *  Only applicable when `type` = `analytics`
     */
    private ?string $urlParametersTemplate = null;

    public function setAddAttachment(?FlowStepAddAttachment $addAttachment = null): self
    {
        $this->addAttachment = $addAttachment;

        return $this;
    }

    public function getAddAttachment(): ?FlowStepAddAttachment
    {
        return $this->addAttachment;
    }

    public function setAggregate(?FlowStepAggregate $aggregate = null): self
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    public function getAggregate(): ?FlowStepAggregate
    {
        return $this->aggregate;
    }

    public function setApplyToLinkDomains(?string $applyToLinkDomains = null): self
    {
        $this->applyToLinkDomains = $applyToLinkDomains;

        return $this;
    }

    public function getApplyToLinkDomains(): ?string
    {
        return $this->applyToLinkDomains;
    }

    public function setArchive(?FlowStepArchive $archive = null): self
    {
        $this->archive = $archive;

        return $this;
    }

    public function getArchive(): ?FlowStepArchive
    {
        return $this->archive;
    }

    public function setDiscard(?FlowStepDiscard $discard = null): self
    {
        $this->discard = $discard;

        return $this;
    }

    public function getDiscard(): ?FlowStepDiscard
    {
        return $this->discard;
    }

    public function setDivisor(?int $divisor = null): self
    {
        $this->divisor = $divisor;

        return $this;
    }

    public function getDivisor(): ?int
    {
        return $this->divisor;
    }

    public function setDnsLookup(?FlowStepDnsLookup $dnsLookup = null): self
    {
        $this->dnsLookup = $dnsLookup;

        return $this;
    }

    public function getDnsLookup(): ?FlowStepDnsLookup
    {
        return $this->dnsLookup;
    }

    public function setEnabledExpression(?string $enabledExpression = null): self
    {
        $this->enabledExpression = $enabledExpression;

        return $this;
    }

    public function getEnabledExpression(): ?string
    {
        return $this->enabledExpression;
    }

    public function setErrorOnNotFound(?bool $errorOnNotFound = null): self
    {
        $this->errorOnNotFound = $errorOnNotFound;

        return $this;
    }

    public function getErrorOnNotFound(): ?bool
    {
        return $this->errorOnNotFound;
    }

    public function setExternalContent(?FlowStepExternalContent $externalContent = null): self
    {
        $this->externalContent = $externalContent;

        return $this;
    }

    public function getExternalContent(): ?FlowStepExternalContent
    {
        return $this->externalContent;
    }

    public function setExternalData(?FlowStepExternalData $externalData = null): self
    {
        $this->externalData = $externalData;

        return $this;
    }

    public function getExternalData(): ?FlowStepExternalData
    {
        return $this->externalData;
    }

    public function setExtractData(?FlowStepExtractData $extractData = null): self
    {
        $this->extractData = $extractData;

        return $this;
    }

    public function getExtractData(): ?FlowStepExtractData
    {
        return $this->extractData;
    }

    public function setId(?string $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setLdapSearch(?FlowStepLdapSearch $ldapSearch = null): self
    {
        $this->ldapSearch = $ldapSearch;

        return $this;
    }

    public function getLdapSearch(): ?FlowStepLdapSearch
    {
        return $this->ldapSearch;
    }

    public function setMailPlusApiCredentials(?MailPlusAPICredentials $mailPlusApiCredentials = null): self
    {
        $this->mailPlusApiCredentials = $mailPlusApiCredentials;

        return $this;
    }

    public function getMailPlusApiCredentials(): ?MailPlusAPICredentials
    {
        return $this->mailPlusApiCredentials;
    }

    public function setOverwriteUrlParameters(?bool $overwriteUrlParameters = null): self
    {
        $this->overwriteUrlParameters = $overwriteUrlParameters;

        return $this;
    }

    public function getOverwriteUrlParameters(): ?bool
    {
        return $this->overwriteUrlParameters;
    }

    public function setResubmitMessage(?FlowStepResubmitMessage $resubmitMessage = null): self
    {
        $this->resubmitMessage = $resubmitMessage;

        return $this;
    }

    public function getResubmitMessage(): ?FlowStepResubmitMessage
    {
        return $this->resubmitMessage;
    }

    public function setRewriteRecipient(?FlowStepRewriteRecipient $rewriteRecipient = null): self
    {
        $this->rewriteRecipient = $rewriteRecipient;

        return $this;
    }

    public function getRewriteRecipient(): ?FlowStepRewriteRecipient
    {
        return $this->rewriteRecipient;
    }

    public function setSchedule(?FlowStepSchedule $schedule = null): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getSchedule(): ?FlowStepSchedule
    {
        return $this->schedule;
    }

    public function setSetHeader(?FlowStepSetHeader $setHeader = null): self
    {
        $this->setHeader = $setHeader;

        return $this;
    }

    public function getSetHeader(): ?FlowStepSetHeader
    {
        return $this->setHeader;
    }

    public function setSetSender(?FlowStepSetSender $setSender = null): self
    {
        $this->setSender = $setSender;

        return $this;
    }

    public function getSetSender(): ?FlowStepSetSender
    {
        return $this->setSender;
    }

    public function setSubjectTemplate(?string $subjectTemplate = null): self
    {
        $this->subjectTemplate = $subjectTemplate;

        return $this;
    }

    public function getSubjectTemplate(): ?string
    {
        return $this->subjectTemplate;
    }

    public function setTemplate(?ObjectDescription $template = null): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): ?ObjectDescription
    {
        return $this->template;
    }

    public function setTo(?string $to = null): self
    {
        $this->to = $to;

        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setType(string|FlowStepType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string|FlowStepType
    {
        return $this->type;
    }

    public function setUrlParametersTemplate(?string $urlParametersTemplate = null): self
    {
        $this->urlParametersTemplate = $urlParametersTemplate;

        return $this;
    }

    public function getUrlParametersTemplate(): ?string
    {
        return $this->urlParametersTemplate;
    }
}
