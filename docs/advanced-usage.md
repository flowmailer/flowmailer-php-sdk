# Advanced Usage 

### Caching

Caching the access token obtained at the first request can speed up most of the following requests, so this is recommended.

You can use any [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible cache, see [simple-cache implementations](https://packagist.org/providers/psr/simple-cache-implementation) on packagist.

In this example we use Symfony Cache:
```bash
composer require symfony/cache
```

```php
<?php

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$cache = new Psr16Cache(new FilesystemAdapter('flowmailer-token', 0, __DIR__.'/cache-dir'));

$flowmailer = Flowmailer::init($accountId, $clientId, $clientSecret, [], null, $cache);
```

## Logging
You can use any [PSR-3](https://www.php-fig.org/psr/psr-3/) compatible logger, see [log implementations](https://packagist.org/providers/psr/log-implementation) on packagist.

In this example we use [Monolog](https://github.com/Seldaek/monolog):
```bash
composer require monolog/monolog
```

```php
<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/** init code from 'Basic usage' */
$flowmailer = Flowmailer::init($accountId, $clientId, $clientSecret);
$flowmailer->setLogger((new Logger('flowmailer'))->pushHandler(new StreamHandler(__DIR__.'/journal.log', Logger::INFO)));

/* rest of code from 'Basic usage' */
```

This will log creation of objects in Flowmailer, like:
```log
[2022-02-21T15:00:01.000000+00:00] flowmailer.INFO: Created: https://api.flowmailer.net/1234/messages/2022022115000101234567890abcdef0 [] []
[2022-02-21T15:00:02.000000+00:00] flowmailer.INFO: Created: https://api.flowmailer.net/1234/messages/2022022115000201234567890abcdef0 [] []
[2022-02-21T15:00:03.000000+00:00] flowmailer.INFO: Created: https://api.flowmailer.net/1234/messages/2022022115000301234567890abcdef0 [] []
```

## Multiple messages

To use this you have to install [Guzzle Promises](https://github.com/guzzle/promises) library.
```bash
composer require guzzlehttp/promises:^2.0
```

```php
<?php

require 'vendor/autoload.php';

use Flowmailer\API\Enum\MessageType;
use Flowmailer\API\Flowmailer;
use Flowmailer\API\Model\SubmitMessage;
use Flowmailer\API\Utility\SubmitMessageCreatorIterator;
use GuzzleHttp\Promise\Each;

// The credentials can be obtained in your Flowmailer account
$accountId    = '...';
$clientId     = '...';
$clientSecret = '...';

$flowmailer = Flowmailer::init($accountId, $clientId, $clientSecret);

// $data is an Iterator containing data for the messages (see below)
$data = new \ArrayIterator([
    'key' => [
        'name'    => 'Full Name',
        'subject' => 'An e-mail message',
        'email'   => 'your-customer@email.org',
    ],
]);
$sender   = 'info@your-company.com';
$callback = function (array $item) use ($sender) {
    return (new SubmitMessage())
        ->setMessageType(MessageType::EMAIL)
        ->setSubject($item['subject'])
        ->setRecipientAddress($item['email'])
        ->setSenderAddress($sender);
};

$results = $flowmailer->submitMessages(new SubmitMessageCreatorIterator($data, $callback));

Each::ofLimit($results, 10,
    function ($result, $key) {
        // The result of a successful API call and the key given by the iterator
    },
    function ($exception, $key) {
        // The exception during an unsuccessful API call and the key given by the iterator
    }
)->wait();
```

For real async handling of responses it is recommended to use [Guzzle 7 HTTP Adapter](https://github.com/php-http/guzzle7-adapter)
```bash
composer require php-http/guzzle7-adapter
```
Or to use [Symfony Curl HttpClient](https://github.com/symfony/http-client)
```bash
composer require symfony/http-client
```

```php
<?php

// Code from setup
use Http\Adapter\Guzzle7\Client;

$guzzle7HttpClient = new Client();
$flowmailer->setHttpClient($guzzle7HttpClient);

// Or
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\HttplugClient;

$symfonyCurlHttpClient = new HttplugClient(new CurlHttpClient());
$flowmailer->setHttpClient($symfonyCurlHttpClient);
```

### ArrayIterator

Note: This is not suited when sending many messages, as it will use too much memory  
```php
<?php

$data = new \ArrayIterator([
    [
        'name'    => 'Full Name',
        'subject' => 'An e-mail message',
        'email'   => 'your-customer@email.org',
    ],
    // Add more rows here
]);
```

### CSV iterator

Note: This is just an example of a possible implementation.

```bash
composer require ogrrd/csv-iterator
```

data.csv:

| name          | subject                | email                         |
|---------------|------------------------|-------------------------------|
| Full Name     | An e-mail message      | your-customer@email.org       |
| Another name  | Another e-mail message | your-other-customer@email.org |
| ... more rows |                        |                               |

```php
<?php

use ogrrd\CsvIterator\CsvIterator;

$data = (new CsvIterator('data.csv'))->useFirstRowAsHeader();
```

### Database iterator

Note: This is just an example of a possible implementation.

Create a sqlite database named data.sqlite3 with a table 'data' in it.
```php
<?php

$pdo = new PDO('sqlite:data.sqlite3');
$pdo->exec('CREATE TABLE IF NOT EXISTS data (id INTEGER PRIMARY KEY, name TEXT, subject TEXT, email TEXT)');

$statement = $pdo->prepare('INSERT INTO data (name, subject, email) VALUES (:name, :subject, :email)');
$statement->bindParam(':name', $name);
$statement->bindParam(':subject', $subject);
$statement->bindParam(':email', $email);

$data = [/* ... */];
foreach ($data as $item) {
    $name    = $item['name'];
    $subject = $item['subject'];
    $email   = $item['email'];

    $statement->execute();
}
```

```php
<?php

use Flowmailer\API\Utility\PdoGeneratorFactory;

$data = (new PdoGeneratorFactory(new PDO('sqlite:data.sqlite3')))->createGenerator('SELECT * FROM data;');
```
