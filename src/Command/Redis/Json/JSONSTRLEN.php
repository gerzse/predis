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

namespace Predis\Command\Redis\Json;

use Predis\Command\Command as RedisCommand;
use Predis\Command\CommandInterface;

/**
 * @see https://redis.io/commands/json.strlen/
 *
 * Report the length of the JSON String at path in key
 */
class JSONSTRLEN extends RedisCommand
{
    public function getId()
    {
        return 'JSON.STRLEN';
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys(): array
    {
        return [$this->getFirstArgument()];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandMode(): string
    {
        return CommandInterface::READ_MODE;
    }
}
