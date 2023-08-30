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

use Predis\Command\Command as RedisCommand;

/**
 * @see http://redis.io/commands/client-list
 * @see http://redis.io/commands/client-kill
 * @see http://redis.io/commands/client-getname
 * @see http://redis.io/commands/client-setname
 */
class CLIENT extends RedisCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'CLIENT';
    }

    public function setArguments(array $arguments)
    {
        switch ($arguments[0]) {
            case 'LIST':
                $this->setListArguments($arguments);
                break;
            case 'NOEVICT':
                $arguments[0] = 'NO-EVICT';
                $this->setNoTouchArguments($arguments);
                break;
            case 'NOTOUCH':
                $arguments[0] = 'NO-TOUCH';
                $this->setNoTouchArguments($arguments);
                break;
            case 'SETINFO':
                $this->setSetInfoArguments($arguments);
                break;
            case 'TRACKING':
                $this->setTrackingArguments($arguments);
                break;
            case 'CACHING':
                $this->setCachingArguments($arguments);
                break;
            default:
                parent::setArguments($arguments);
        }
    }

    /**
     * @param  array $arguments
     * @return void
     */
    private function setListArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0]];

        if (array_key_exists(1, $arguments) && null !== $arguments[1]) {
            array_push($processedArguments, 'TYPE', strtoupper($arguments[1]));
        }

        if (array_key_exists(2, $arguments)) {
            array_push($processedArguments, 'ID', $arguments[2]);
        }

        if (count($arguments) > 3) {
            for ($i = 3, $iMax = count($arguments); $i < $iMax; $i++) {
                $processedArguments[] = $arguments[$i];
            }
        }

        parent::setArguments($processedArguments);
    }

    /**
     * @param  array $arguments
     * @return void
     */
    private function setNoTouchArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0]];

        if (array_key_exists(1, $arguments) && null !== $arguments[1]) {
            $modifier = ($arguments[1]) ? 'ON' : 'OFF';
            $processedArguments[] = $modifier;
        }

        parent::setArguments($processedArguments);
    }

    /**
     * @param  array $arguments
     * @return void
     */
    private function setSetInfoArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0]];

        if (
            array_key_exists(1, $arguments)
            && null !== $arguments[1]
            && array_key_exists(2, $arguments)
            && null !== $arguments[2]
        ) {
            array_push($processedArguments, strtoupper($arguments[1]), $arguments[2]);
        }

        parent::setArguments($processedArguments);
    }

    /**
     * @param  array $arguments
     * @return void
     */
    private function setTrackingArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0]];
        $processedArguments[] = ($arguments[1]) ? 'ON' : 'OFF';

        if (array_key_exists(2, $arguments) && null !== $arguments[2]) {
            $processedArguments = array_merge($processedArguments, $arguments[2]->toArray());
        }

        parent::setArguments($processedArguments);
    }

    /**
     * @param  array $arguments
     * @return void
     */
    private function setCachingArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0]];
        $processedArguments[] = ($arguments[1]) ? 'YES' : 'NO';

        parent::setArguments($processedArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        $args = array_change_key_case($this->getArguments(), CASE_UPPER);

        switch (strtoupper($args[0])) {
            case 'LIST':
                return $this->parseClientList($data);
            case 'TRACKINGINFO':
                return $this->parseTrackingInfo($data);
            case 'KILL':
            case 'GETNAME':
            case 'SETNAME':
            default:
                return $data;
        } // @codeCoverageIgnore
    }

    /**
     * Parses the response to CLIENT LIST and returns a structured list.
     *
     * @param string $data Response buffer.
     *
     * @return array
     */
    protected function parseClientList($data)
    {
        $clients = [];

        foreach (explode("\n", $data, -1) as $clientData) {
            $client = [];

            foreach (explode(' ', $clientData) as $kv) {
                @[$k, $v] = explode('=', $kv);
                $client[$k] = $v;
            }

            $clients[] = $client;
        }

        return $clients;
    }

    /**
     * @param        $data
     * @return array
     */
    protected function parseTrackingInfo($data): array
    {
        $parsedData = [];

        for ($i = 0, $iMax = count($data); $i <= $iMax; $i++) {
            if (array_key_exists($i + 1, $data)) {
                $parsedData[$data[$i]] = $data[++$i];
            }
        }

        return $parsedData;
    }

    /**
     * @param                          $data
     * @return array|mixed|string|null
     */
    public function parseResp3Response($data)
    {
        return $data;
    }
}
