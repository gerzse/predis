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
 * @see http://redis.io/commands/sort
 */
class SORT extends RedisCommand implements Clusterable
{
    use ClusterableContract;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SORT';
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments)
    {
        if (count($arguments) === 1) {
            parent::setArguments($arguments);

            return;
        }

        $query = [$arguments[0]];
        $sortParams = array_change_key_case($arguments[1], CASE_UPPER);

        if (isset($sortParams['BY'])) {
            $query[] = 'BY';
            $query[] = $sortParams['BY'];
        }

        if (isset($sortParams['GET'])) {
            $getargs = $sortParams['GET'];

            if (is_array($getargs)) {
                foreach ($getargs as $getarg) {
                    $query[] = 'GET';
                    $query[] = $getarg;
                }
            } else {
                $query[] = 'GET';
                $query[] = $getargs;
            }
        }

        if (isset($sortParams['LIMIT'])
            && is_array($sortParams['LIMIT'])
            && count($sortParams['LIMIT']) == 2) {
            $query[] = 'LIMIT';
            $query[] = $sortParams['LIMIT'][0];
            $query[] = $sortParams['LIMIT'][1];
        }

        if (isset($sortParams['SORT'])) {
            $query[] = strtoupper($sortParams['SORT']);
        }

        if (isset($sortParams['ALPHA']) && $sortParams['ALPHA'] == true) {
            $query[] = 'ALPHA';
        }

        if (isset($sortParams['STORE'])) {
            $query[] = 'STORE';
            $query[] = $sortParams['STORE'];
        }

        parent::setArguments($query);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = "$prefix{$arguments[0]}";

            if (($count = count($arguments)) > 1) {
                for ($i = 1; $i < $count; ++$i) {
                    switch (strtoupper($arguments[$i])) {
                        case 'BY':
                        case 'STORE':
                            $arguments[$i] = "$prefix{$arguments[++$i]}";
                            break;

                        case 'GET':
                            $value = $arguments[++$i];
                            if ($value !== '#') {
                                $arguments[$i] = "$prefix$value";
                            }
                            break;

                        case 'LIMIT':
                            $i += 2;
                            break;
                    }
                }
            }

            $this->setRawArguments($arguments);
        }
    }

    public function getKeys(): ?array
    {
        $arguments = $this->getArguments();
        $firstKey = $arguments[0];

        if (1 === $argc = count($arguments)) {
            return [$firstKey];
        }

        $keys = [$firstKey];

        for ($i = 1; $i < $argc; ++$i) {
            if (strtoupper($arguments[$i]) === 'STORE') {
                $keys[] = $arguments[++$i];
            }
        }

        return $keys;
    }
}
