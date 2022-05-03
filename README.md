# Flowmailer PHP SDK

Flowmailers PHP 8.1 SDK for api.flowmailer.net REST API

## Getting started

### Installation

See [docs.php-http.org](https://docs.php-http.org/en/latest/httplug/users.html) for details on the HttpClient discovery.

A normal installation would be something equal to:
```bash
$ composer require symfony/http-client nyholm/psr7 flowmailer/flowmailer-php-sdk
```

Choose your preferred [client implementations](https://packagist.org/providers/psr/http-client-implementation) on packagist.

### Basic usage
```php
<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Flowmailer\API\Flowmailer;
use Flowmailer\API\Model\SubmitMessage;

// The credentials can be obtained in your Flowmailer account
$accountId    = '...';
$clientId     = '...';
$clientSecret = '...';

$flowmailer = Flowmailer::init($accountId, $clientId, $clientSecret);

$submitMessage = (new SubmitMessage())
    ->setMessageType('EMAIL')
    ->setSubject('An e-mail message')
    ->setRecipientAddress('your-customer@email.org')
    ->setSenderAddress('info@your-company.com')
;

$result = $flowmailer->submitMessage($submitMessage);
```

### Advanced usage

See [advanced usage] for examples on caching, logging and sending multiple messages at once.
See [queue messages] for info on queueing messages for later consumption.

[advanced usage]: docs/advanced-usage.md "See advanced usage"
[queue messages]: docs/queue-messages.md "See queue messages"
