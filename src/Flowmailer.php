<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API;

use Composer\InstalledVersions;
use Flowmailer\API\Collection\ErrorCollection;
use Flowmailer\API\Collection\NextRangeHolderCollection;
use Flowmailer\API\Exception\BadRequestException;
use Flowmailer\API\Exception\ForbiddenException;
use Flowmailer\API\Exception\NotFoundException;
use Flowmailer\API\Exception\ServerException;
use Flowmailer\API\Exception\UnauthorizedException;
use Flowmailer\API\Logger\Journal;
use Flowmailer\API\Model\Errors;
use Flowmailer\API\Model\OAuthErrorResponse;
use Flowmailer\API\Parameter\ContentRange;
use Flowmailer\API\Parameter\ReferenceRange;
use Flowmailer\API\Plugin\AuthTokenPlugin;
use Flowmailer\API\Serializer\ResponseData;
use Flowmailer\API\Serializer\SerializerFactory;
use Flowmailer\API\Utility\SubmitMessageCreatorIterator;
use GuzzleHttp\Promise\Promise;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpAsyncClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\UnicodeString;

class Flowmailer extends Endpoints implements FlowmailerInterface
{
    final public const API_VERSION = 'v1.12';

    private readonly string $accountId;

    private readonly string $clientId;

    private readonly string $clientSecret;

    private ?ClientInterface $httpClient = null;

    private ?ClientInterface $authClient = null;

    private readonly RequestFactoryInterface $requestFactory;

    private readonly UriFactoryInterface $uriFactory;

    private readonly StreamFactoryInterface $streamFactory;

    /**
     * @var array|Plugin[]
     */
    private ?array $plugins = null;

    public function __construct(
        private readonly OptionsInterface $options,
        private ?LoggerInterface $logger = null,
        private readonly ?CacheInterface $cache = null,
        private ?ClientInterface $innerHttpClient = null,
        private readonly ?ClientInterface $innerAuthClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?UriFactoryInterface $uriFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?SerializerInterface $serializer = null
    ) {
        $this->logger ??= new NullLogger();

        $this->accountId    = $options->getAccountId();
        $this->clientId     = $options->getClientId();
        $this->clientSecret = $options->getClientSecret();

        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->uriFactory     = $uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
        $this->streamFactory  = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();

        parent::__construct($serializer ?? SerializerFactory::create());
    }

    public static function init(string $accountId, string $clientId, string $clientSecret, array $options = [], ...$additionalArgs): FlowmailerInterface
    {
        $options['account_id']    = $accountId;
        $options['client_id']     = $clientId;
        $options['client_secret'] = $clientSecret;

        return new static(new Options($options), ...$additionalArgs);
    }

    public function request($method, $path, array $parameters = [], ?string $type = null, bool $autoAddAccountId = true)
    {
        $parameters = new CustomRequestOptions($parameters);
        if ($autoAddAccountId) {
            $path = sprintf('/%1$s%2$s', $this->getOptions()->getAccountId(), $path);
        }

        preg_match_all('#{(.*?)}#s', (string) $path, $matches);
        foreach ($matches[0] as $index => $value) {
            $path = str_replace($value, $parameters->getPath()[$matches[1][$index]], (string) $path);
        }

        $request  = $this->createRequest($method, $path, $parameters->getBody(), $parameters->getMatrices(), $parameters->getQuery(), $parameters->getHeaders());
        $response = $this->handleResponse($this->getResponse($request), (string) $request->getBody(), $request->getMethod());

        if (is_null($type)) {
            return $response;
        }

        $items = $this->serializer->deserialize($response, $type, 'json');
        if ($response instanceof ResponseData && is_subclass_of($type, NextRangeHolderCollection::class)) {
            if ($response->getMeta('next-range') instanceof ReferenceRange) {
                $items->setNextRange($response->getMeta('next-range'));
            }
            if ($response->getMeta('content-range') instanceof ContentRange) {
                $items->setContentRange($response->getMeta('content-range'));
            }
        }

        return $items;
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

    public function setHttpClient(?ClientInterface $httpClient = null): FlowmailerInterface
    {
        $this->innerHttpClient = $httpClient ?? $this->innerHttpClient ?? Psr18ClientDiscovery::find();

        $this->httpClient = new PluginClient(
            $this->innerHttpClient,
            $this->getPlugins()
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

    public function setLogger(?LoggerInterface $logger = null): FlowmailerInterface
    {
        $this->logger = $logger ?? new NullLogger();

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getOptions(): OptionsInterface
    {
        return $this->options;
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    public function withAccountId(string $id): FlowmailerInterface
    {
        return new Flowmailer(
            (clone $this->getOptions())->setAccountId($id),
            $this->logger,
            $this->cache,
            $this->innerHttpClient,
            $this->innerAuthClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->streamFactory,
            $this->serializer
        );
    }

    public function handleResponse(ResponseInterface $response, $body = null, $method = '')
    {
        $responseBody = $response->getBody()->getContents();

        if (is_null($body) === false) {
            if ($response->getHeaderLine('content-length') === '0' && ($location = $response->getHeaderLine('location'))) {
                $locationParts = (new UnicodeString($location))->split('/');
                $responseBody  = (string) end($locationParts);
            }
        }
        if (($method === 'DELETE' || ($method === 'PUT' && $responseBody == '')) && $response->getStatusCode() === 200) {
            return true;
        }

        $meta = [];
        if ($response->hasHeader('next-range')) {
            $meta['next-range'] = ReferenceRange::fromString(current($response->getHeader('next-range')));
        }
        if ($response->hasHeader('content-range')) {
            $meta['content-range'] = ContentRange::fromString(current($response->getHeader('content-range')));
        }

        return new ResponseData($responseBody, $meta);
    }

    private function handleResponseError(ResponseInterface $response)
    {
        $responseBody = $response->getBody()->getContents();

        if ($response->getStatusCode() == 400 || $response->getStatusCode() == 403 || $response->getStatusCode() == 404) {
            /** @var Errors $errors */
            $errors = $this->serializer->deserialize($responseBody, Errors::class, 'json');

            $exception = null;
            if (is_null($errors->getAllErrors())) {
                // TODO: handle error responses from oauth module
                // Example: {"error":"invalid_scope","error_description":"Invalid scope: flowmailer","scope":"api"}
                $exception = new \Exception($responseBody);
                $errors->setAllErrors(new ErrorCollection([]));
            }
            foreach ($errors->getAllErrors() as $error) {
                $object  = (new UnicodeString($error->getObjectName() ?: ''))->trimPrefix('rest')->toString();
                $field   = $error->getField() ?: '';
                $message = $error->getDefaultMessage() ?: '';

                $className = sprintf('Flowmailer\\API\\Model\\%s', $object);
                if (class_exists($className)) {
                    $object = $className;
                }

                $code = $error->getCode();

                $exception = new \Exception(trim(implode(' ', [implode('.', array_filter([$object, $field])), $message, $code])), 0, $exception);
            }

            if ($response->getStatusCode() == 400) {
                $exception = new BadRequestException($exception->getMessage(), $exception->getCode());
            } elseif ($response->getStatusCode() == 403) {
                $exception = new ForbiddenException($exception->getMessage(), $exception->getCode());
            } else {
                $exception = new NotFoundException($exception->getMessage(), $exception->getCode());
            }
            $exception->setErrors($errors->getAllErrors());

            return $exception;
        } elseif ($response->getStatusCode() == 401) {
            /* @var OAuthErrorResponse $oAuthError */
            try {
                $oAuthError = $this->serializer->deserialize($responseBody, OAuthErrorResponse::class, 'json');
            } catch (NotEncodableValueException $exception) {
                return new UnauthorizedException(sprintf('Unable to authenticate: %s', str_replace([PHP_EOL], '', strip_tags($responseBody))), $response->getStatusCode(), $exception);
            }

            if (is_null($oAuthError) === false) {
                return new UnauthorizedException($oAuthError->getErrorDescription());
            }
        } elseif ($response->getStatusCode() == 500) {
            return new ServerException(sprintf('Internal Server Error: %s', str_replace([PHP_EOL], '', strip_tags($responseBody))), $response->getStatusCode());
        }

        return new ServerException(sprintf('Something went wrong with status (%s): %s', $response->getStatusCode(), str_replace([PHP_EOL], '', strip_tags($responseBody))), $response->getStatusCode());
    }

    /**
     * Send email or sms messages.
     *
     * @param \Iterator $submitMessages
     */
    public function submitMessages(SubmitMessageCreatorIterator $submitMessages): \Generator
    {
        if (InstalledVersions::isInstalled('guzzlehttp/promises') === false) {
            throw new \Exception('To use Flowmailer::submitMessages you should install the guzzlehttp/promises library (composer require guzzlehttp/promises)');
        }

        $client = $this->getHttpClient();

        if ($client instanceof HttpAsyncClient === false) {
            throw new \Exception(sprintf('The client used for calling submitMessages should be an %s. Choose one of this clients: https://packagist.org/providers/php-http/async-client-implementation', HttpAsyncClient::class));
        }

        foreach ($submitMessages as $key => $submitMessage) {
            $request = $this->createRequestForSubmitMessage($submitMessage);

            $promiseHolder = new \stdClass();
            try {
                $promiseHolder->promise = $client->sendAsyncRequest($request);
            } catch (\Exception $exception) {
                $guzzlePromise = new Promise();
                $guzzlePromise->reject($exception);

                yield $key => $guzzlePromise;

                continue;
            }

            $guzzlePromise = new Promise(function () use ($promiseHolder) {
                try {
                    $promiseHolder->promise->wait();
                } catch (\Exception) {
                    // Exception should already be propagated to $guzzlePromise via the ->then callbacks
                }
            });

            $promiseHolder->promise = $promiseHolder->promise->then(
                function ($response) use ($guzzlePromise, $request) {
                    try {
                        $responseData = $this->handleResponse($response, (string) $request->getBody(), $request->getMethod());
                        $guzzlePromise->resolve($responseData);
                    } catch (\Exception $exception) {
                        $guzzlePromise->reject($exception);
                    }

                    return $response;
                },
                function ($exception) use ($guzzlePromise) {
                    try {
                        if ($exception instanceof ClientErrorException) {
                            /** @var ApiException $responseError */
                            $responseError = $this->handleResponseError($exception->getResponse());
                            $guzzlePromise->reject($responseError);

                            return $exception->getResponse(); // Required to prevent exception on react client
                        } else {
                            $guzzlePromise->reject($exception);
                        }
                    } catch (\Exception $exception) {
                        $guzzlePromise->reject($exception);
                    }

                    throw $exception;
                }
            );

            yield $key => $guzzlePromise;
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

        $matrices = array_filter($matrices, fn ($value) => is_null($value) === false && $value !== '');

        foreach ($matrices as $matrixName => $matrixValue) {
            if ($matrixValue instanceof \Stringable) {
                $matrixValue = (string) $matrixValue;
            } elseif (is_bool($matrixValue)) {
                $matrixValue = ($matrixValue === false) ? 'false' : 'true';
            }

            $matrices[$matrixName] = implode(',', (array) $matrixValue);
        }
        if (($matricesString = http_build_query($matrices, '', ';')) !== '') {
            $path = sprintf('%s;%s', $path, rawurldecode($matricesString));
        }

        foreach ($query as $queryName => $queryValue) {
            if ($queryValue instanceof \Stringable) {
                $query[$queryName] = (string) $queryValue;
            }
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

    public function getResponse(RequestInterface $request, ?ClientInterface $client = null): ResponseInterface
    {
        $client ??= $this->getHttpClient();

        try {
            $response = $client->sendRequest($request);
        } catch (ClientErrorException $exception) {
            throw $this->handleResponseError($exception->getResponse());
        }

        return $response;
    }

    /**
     * @return array|Plugin[]
     */
    protected function getPlugins(): array
    {
        if (is_null($this->plugins)) {
            $this->plugins = [
                'history'    => new HistoryPlugin(new Journal($this->logger)),
                'header_set' => new HeaderSetPlugin($this->options->getPlugin('header_set')),
                'retry'      => new RetryPlugin($this->options->getPlugin('retry')),
                'error'      => new ErrorPlugin($this->options->getPlugin('error')),
                'auth_token' => new AuthTokenPlugin($this, $this->options, $this->cache),
            ];

            if (class_exists(LoggerPlugin::class)) {
                $this->plugins['logger'] = new LoggerPlugin($this->logger);
            }
        }

        return $this->plugins;
    }

    /**
     * @param array|Plugin[] $plugins
     */
    protected function setPlugins(array $plugins): FlowmailerInterface
    {
        $this->plugins = $plugins;

        return $this;
    }

    protected function addPlugin(string $key, Plugin $plugin): FlowmailerInterface
    {
        $this->plugins = $this->getPlugins();

        $this->plugins[$key] = $plugin;

        return $this;
    }

    protected function removePlugin(string $key)
    {
        $this->plugins = $this->getPlugins();

        unset($this->plugins[$key]);

        return $this;
    }
}
