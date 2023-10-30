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

namespace Predis\Command\Redis;

use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\BitByte;

/**
 * @see http://redis.io/commands/bitpos
 *
 * Return the position of the first bit set to 1 or 0 in a string.
 */
class BITPOS extends RedisCommand
{
    use BitByte;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BITPOS';
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

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
