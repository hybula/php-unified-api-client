# Unified API Client (PHP)
Official client for our Unified API using PHP.

## Requirements
- PHP 7.4 or newer
- cURL extension
- JSON extension
- OpenSSL extension

## Installation
You may install it using Composer:
```
composer require hybula/php-unified-api-client
```

## Example
```php
<?php

require __DIR__.'/vendor/autoload.php';

use Hybula\Unified;

$api = new Client();
$api->setAuthCredentials('apiKey', 'apiToken');
$api->setApiCore('capsule');
$apiCall = $api->apiCall('POST', 'domain.com/letsencrypt', ['type' => 'letsencrypt-ecc']);
var_dump($apiCall);
```

or simply load the class directly if you are not using Composer:
```php
require __DIR__.'/src/Hybula/Unified/Client.php';
$api = new \Hybula\Unified\Client();
```

## Security
At Hybula we always develop secure-by-design, however as we are human too, security vulnerabilities or issues are always possible. If you found anything please let us know as soon as possible through email at: pgp(at)hybula.com
Please do not report security related issues through GitHub.

## License
Mozilla Public License Version 2.0