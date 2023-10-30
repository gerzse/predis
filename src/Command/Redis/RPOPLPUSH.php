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

use Predis\Command\PrefixableCommand as RedisCommand;

/**
 * @see http://redis.io/commands/rpoplpush
 */
class RPOPLPUSH extends RedisCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RPOPLPUSH';
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys(): array
    {
        return $this->getArguments();
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
