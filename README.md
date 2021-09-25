# Unified API Client (PHP)
Official client for our Unified API using PHP.

## Installation
You may install it using Composer:
```
composer install hybula/php-unified-api-client
```

## Example
```php
<?php

require __DIR__.'/vendor/autoload.php';

use Hybula\Unified;

$api = new Client();
$api->setAuthCredentials('apiKey', 'apiToken');
$api->setApiProduct('capsule');
$api->setDomain('domain.com');
$apiCall = $api->apiCall('POST', 'letsencrypt', ['type' => 'letsencrypt-ecc']);
var_dump($apiCall);
```

or simply load the class directly if you are not using Composer:
```php
require __DIR__.'/src/Hybula/Unified/Client.php';
$api = new \Hybula\Unified\Client();
```

## License
Mozilla Public License Version 2.0