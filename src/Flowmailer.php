<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Composer\InstalledVersions;
use Flowmailer\API\Logger\Journal;
use Flowmailer\API\Model\Errors;
use Flowmailer\API\Model\OAuthErrorResponse;
use Flowmailer\API\Plugin\AuthTokenPlugin;
use Flowmailer\API\Serializer\SerializerFactory;
use Flowmailer\API\Utility\SubmitMessageCreatorIterator;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpAsyncClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\UnicodeString;

class Flowmailer extends Endpoints
{
    public const API_VERSION = 'v1.12';

    /**
     * @var Options
     */
    private $options;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var LoggerInterface|null
     */
    private $journalLogger;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var ClientInterface
     */
    private $innerHttpClient;

    /**
     * @var ClientInterface
     */
    private $authClient;

    /**
     * @var ClientInterface
     */
    private $innerAuthClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriFactory
     */
    private $uriFactory;

    /**
     * @var StreamFactory|null
     */
    private $streamFactory;

    public function __construct(
        Options $options,
        CacheInterface $cache = null,
        LoggerInterface $journalLogger = null,
        ClientInterface $httpClient = null,
        ClientInterface $authClient = null,
        RequestFactoryInterface $requestFactory = null,
        UriFactory $uriFactory = null,
        StreamFactory $streamFactory = null,
        SerializerInterface $serializer = null
    ) {
        $this->options = $options;

        $this->accountId    = $options->getAccountId();
        $this->clientId     = $options->getClientId();
        $this->clientSecret = $options->getClientSecret();

        $this->cache         = $cache;
        $this->journalLogger = $journalLogger;

        $this->innerHttpClient = $httpClient;
        $this->innerAuthClient = $authClient;

        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->uriFactory     = $uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
        $this->streamFactory  = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();

        parent::__construct($serializer ?? SerializerFactory::create());
    }

    public static function init(string $accountId, string $clientId, string $clientSecret, array $options = [], ...$additionalArgs): self
    {
        $options['account_id']     = $accountId;
        $options['client_id']      = $clientId;
        $options['client_secret']  = $clientSecret;

        return new self(new Options($options), ...$additionalArgs);
    }

    public function setAuthClient(?ClientInterface $authClient = null)
    {
        $this->authClient = new PluginClient(
            $authClient ?? $this->innerAuthClient ?? Psr18ClientDiscovery::find(),
            [
                new HeaderSetPlugin([
                    'Accept'       => sprintf('application/vnd.flowmailer.%s+json', self::API_VERSION),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]),
                new ErrorPlugin(),
            ]
        );
    }

    public function getAuthClient(): ClientInterface
    {
        if (is_null($this->authClient)) {
            $this->setAuthClient();
        }

        return $this->authClient;
    }

    public function setHttpClient(?HttpAsyncClient $httpClient = null): self
    {
        $this->innerHttpClient = $httpClient ?? $this->innerHttpClient ?? Psr18ClientDiscovery::find();

        $this->httpClient = new PluginClient(
            $this->innerHttpClient,
            [
                new HistoryPlugin(new Journal($this->journalLogger)),
                new HeaderSetPlugin([
                    'Accept'       => sprintf('application/vnd.flowmailer.%s+json', self::API_VERSION),
                    'Content-Type' => sprintf('application/vnd.flowmailer.%s+json', self::API_VERSION),
                    'Connection'   => 'Keep-Alive',
                    'Keep-Alive'   => '300',
                    'User-Agent'   => sprintf('FlowMailer PHP SDK %s for API %s', InstalledVersions::getVersion('flowmailer/flowmailer-php-sdk'), self::API_VERSION),
                ]),
                new RetryPlugin([
                    'retries' => 3,
                ]),
                new ErrorPlugin(),
                new AuthTokenPlugin($this, $this->options, $this->cache),
            ]
        );

        return $this;
    }

    public function getHttpClient(): ClientInterface
    {
        if (is_null($this->httpClient)) {
            $this->setHttpClient();
        }

        return $this->httpClient;
    }

    public function setLogger(LoggerInterface $journalLogger = null): self
    {
        $this->journalLogger = $journalLogger;

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->journalLogger;
    }

    protected function getOptions(): Options
    {
        return $this->options;
    }

    protected function handleResponse(ResponseInterface $response, $body = null, $method = '')
    {
        $responseBody = $response->getBody()->getContents();

        if (is_null($body) === false) {
            if ($response->getHeaderLine('content-length') === '0' && ($location = $response->getHeaderLine('location'))) {
                $locationParts = (new UnicodeString($location))->split('/');
                $responseBody  = (string) end($locationParts);
            }
        }
        if ($method === 'DELETE' && $response->getStatusCode() === 200) {
            return true;
        }

        return $responseBody;
    }

    private function handleResponseError(ResponseInterface $response)
    {
        $responseBody = $response->getBody()->getContents();

        if ($response->getStatusCode() == 400 || $response->getStatusCode() == 403) {
            /** @var Errors $errors */
            $errors = $this->serializer->deserialize($responseBody, Errors::class, 'json');

            $exception = null;
            foreach ($errors->getAllErrors() as $error) {
                $object  = (new UnicodeString($error->getObjectName() ?: ''))->trimPrefix('rest')->toString();
                $field   = $error->getField() ?: '';
                $message = $error->getDefaultMessage() ?: '';

                $className = sprintf('Flowmailer\\API\\Model\\%s', $object);
                if (class_exists($className)) {
                    $object = $className;
                }

                $code = $error->getCode();

                $exception = new \Exception(implode(' ', [implode('.', array_filter([$object, $field])), $message, $code]), 0, $exception);
            }

            return $exception;
        } elseif ($response->getStatusCode() == 401) {
            /* @var OAuthErrorResponse $oAuthError */
            try {
                $oAuthError = $this->serializer->deserialize($responseBody, OAuthErrorResponse::class, 'json');
            } catch (NotEncodableValueException $exception) {
                return new \Exception('Internal Server Error');
            }

            if (is_null($oAuthError) === false) {
                return new \Exception($oAuthError->getErrorDescription());
            }
        } elseif ($response->getStatusCode() == 500) {
            return new \Exception('Internal Server Error');
        }

        return new \Exception('Internal Server Error');
    }

    /**
     * Send email or sms messages.
     *
     * @param $submitMessages \Iterator
     */
    public function submitMessages(SubmitMessageCreatorIterator $submitMessages): \Generator
    {
        $client = $this->getHttpClient();

        if ($client instanceof HttpAsyncClient === false) {
            throw new \Exception(sprintf('The client used for calling submitMessages should be an %s. Choose one of this clients: https://packagist.org/providers/php-http/async-client-implementation', HttpAsyncClient::class));
        }

        foreach ($submitMessages as $submitMessage) {
            $request = $this->createRequestForSubmitMessage($submitMessage);
            yield $client->sendAsyncRequest($request);
        }
    }

    protected function createAuthRequest($method, $path, $formData): RequestInterface
    {
        $base = $this->options->getAuthBaseUrl();
        $uri  = $this->uriFactory->createUri(sprintf('%s%s', $base, $path));

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody(
                $this->streamFactory->createStream(
                    http_build_query($formData, '', '&')
                )
            );

        return $request;
    }

    protected function createRequest($method, $path, $body, array $matrices, array $query, array $headers): RequestInterface
    {
        $base = $this->options->getBaseUrl();

        $matrices = array_filter($matrices);
        foreach ($matrices as $matrixName => $matrixValue) {
            $matrices[$matrixName] = (string) $matrixValue;
        }
        if (($matricesString = http_build_query($matrices, '', ';')) !== '') {
            $path = sprintf('%s;%s', $path, rawurldecode($matricesString));
        }

        $uri = $this->uriFactory->createUri(sprintf('%s%s', $base, $path));
        $uri = $uri->withQuery(http_build_query($query));

        $request = $this->requestFactory->createRequest($method, $uri);
        if (is_null($body) === false) {
            $request = $request->withBody(
                $this->streamFactory->createStream(
                    $this->serializer->serialize($body, 'json')
                )
            );
        }

        foreach (array_filter($headers) as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, (string) $headerValue);
        }

        return $request;
    }

    protected function getResponse(RequestInterface $request, ClientInterface $client = null): ResponseInterface
    {
        $client = $client ?? $this->getHttpClient();

        try {
            $response = $client->sendRequest($request);
        } catch (ClientErrorException $exception) {
            throw $this->handleResponseError($exception->getResponse());
        }

        return $response;
    }
}
