<?php

/*
 * This file is part of the Predis package.
 *
 * (c) 2009-2020 Daniele Alessandri
 * (c) 2021-2023 Till Krüss
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Predis\Client;

require __DIR__ . '/../autoload.php';

if (PHP_SAPI !== 'fpm-fcgi') {
    exit('This example available only in FPM mode.');
}

// 1. Create client with enabled cache and ttl 2 seconds.
$client = new Client(
    [
        'tcp://127.0.0.1:6372?read_write_timeout=0',
        'tcp://127.0.0.1:6373?read_write_timeout=0',
        'tcp://127.0.0.1:6374?read_write_timeout=0',
    ], [
    'cluster' => 'redis',
    'cache' => 1,
    'cache_config' => ['ttl' => 2],
]);

// 2. Set key into Redis storage.
$client->set('foo', 'bar');

// 3. Retrieves from Redis storage and cache response for 2 seconds.
var_dump($client->get('foo'));

// 4. Set new value for the same key. Just after this invalidation should happen.
// If not then we have updated value in Redis storage and old value in cache.
$client->set('foo', 'baz');

// 5. Retrieves value from cache.
var_dump($client->get('foo'));

// 6. Sleep until TTL expired.
sleep(3);

// 7. Retrieves update value from Redis storage again.
var_dump($client->get('foo'));
