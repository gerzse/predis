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

namespace Predis\Command\Redis\BloomFilter;

use Predis\Command\Command as RedisCommand;
use Predis\Command\CommandInterface;

/**
 * @see https://redis.io/commands/bf.mexists/
 *
 * Determines if one or more items may exist in the filter or not.
 */
class BFMEXISTS extends RedisCommand
{
    public function getId()
    {
        return 'BF.MEXISTS';
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
