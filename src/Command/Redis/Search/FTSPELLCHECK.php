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

namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;
use Predis\Command\CommandInterface;

class FTSPELLCHECK extends RedisCommand
{
    public function getId()
    {
        return 'FT.SPELLCHECK';
    }

    public function setArguments(array $arguments)
    {
        [$index, $query] = $arguments;
        $commandArguments = [];

        if (!empty($arguments[2])) {
            $commandArguments = $arguments[2]->toArray();
        }

        parent::setArguments(array_merge(
            [$index, $query],
            $commandArguments
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys(): array
    {
        return [$this->getArgument(0)];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandMode(): string
    {
        return CommandInterface::READ_MODE;
    }
}
