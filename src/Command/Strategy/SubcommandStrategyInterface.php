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

namespace Predis\Command\Strategy;

interface SubcommandStrategyInterface
{
    /**
     * Process arguments for given subcommand.
     *
     * @param  array $arguments
     * @return array
     */
    public function processArguments(array $arguments): array;

    /**
     * Parse response for given subcommand.
     *
     * @param  mixed $data
     * @return mixed
     */
    public function parseResponse($data);
}
