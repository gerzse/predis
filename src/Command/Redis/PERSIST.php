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

use Predis\Command\Clusterable;
use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Contract\ClusterableContract;

/**
 * @see http://redis.io/commands/persist
 */
class PERSIST extends RedisCommand implements Clusterable
{
    use ClusterableContract;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PERSIST';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }

    public function getKeys(): ?array
    {
        return [$this->getArgument(0)];
    }
}
