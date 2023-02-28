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
use Predis\Command\Argument\Search\Schema;

require __DIR__ . '/../../shared.php';

// Example of FT.ALTER command usage:

// 1. Create index
$client = new Client();

$schema = new Schema();
$schema->addTextField('text_field');
$client->ftcreate('index_alter', $schema);

// 2. Add additional attribute to existing index
$schema = new Schema(true);
$schema->addTextField('new_field_name');

$response = $client->ftalter('index_alter', $schema);

echo 'Response:' . "\n";
print_r($response);
