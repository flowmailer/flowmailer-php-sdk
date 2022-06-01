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

#### Submit a message
```php
```

#### Loop over messages that were submitted earlier
```php
<?php

use Flowmailer\API\Collection\MessageCollection;

$flowmailer           = Flowmailer::init($accountId, $clientId, $clientSecret);
$pageSize             = 100;
$savedReferenceOrNull = null; // Get reference from database or cache (null will start from the beginning of the list)
$referenceRange       = new ReferenceRange($pageSize, $savedReferenceOrNull);

while ($referenceRange instanceof ReferenceRange) {
    /** @var MessageCollection $result */
    $result = $flowmailer->getMessages($referenceRange);

    // Do stuff with the result here

    // Store $referenceRange->getReference() value here in database or cache as input for a future run
    // Now we set the ReferenceRange for the next loop in memory
    $referenceRange = $result->getNextRange();
}
```

### Advanced usage

See [advanced usage] for examples on caching, logging and sending multiple messages at once.  
See [queue messages] for info on queueing messages for later consumption.

[advanced usage]: docs/advanced-usage.md "See advanced usage"
[queue messages]: docs/queue-messages.md "See queue messages"
