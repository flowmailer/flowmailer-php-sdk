# Queue messages 

See [PHP Enqueue] for more info 

### Setup

This example is for the _File system_ based queue, for other implementations see [PHP Enqueue transports]

```bash
composer require enqueue/simple-client enqueue/fs
```

```php
<?php

use Flowmailer\API\Flowmailer;
use Flowmailer\API\SubmitMessageQueue;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

// The credentials can be obtained in your Flowmailer account
$accountId    = '...';
$clientId     = '...';
$clientSecret = '...';
$queueDSN     = sprintf('file://%s/message-queue', __DIR__);

$logger = (new Logger('flowmailer'))->pushHandler(new StreamHandler(__DIR__.'/journal.log', Logger::INFO));
$cache  = new Psr16Cache(new FilesystemAdapter('flowmailer-token', 0, __DIR__.'/cache-dir'));

$flowmailer      = Flowmailer::init($accountId, $clientId, $clientSecret, [], $logger, $cache);
$flowmailerQueue = SubmitMessageQueue::init($flowmailer, $queueDSN);
```

### Producing a message
```php
<?php

use Flowmailer\API\Enum\MessageType;
use Flowmailer\API\Model\SubmitMessage;

// Code from setup

$submitMessage = (new SubmitMessage())
    ->setMessageType(MessageType::EMAIL)
    ->setSubject('An e-mail message')
    ->setRecipientAddress('your-customer@email.org')
    ->setSenderAddress('info@your-company.com')
;

$flowmailerQueue->submitMessage($submitMessage);
```

### Consuming messages
```php
<?php

// Code from setup
$flowmailerQueue->consume();
```

[PHP Enqueue]: https://php-enqueue.github.io/
[PHP Enqueue transports]: https://php-enqueue.github.io/transport