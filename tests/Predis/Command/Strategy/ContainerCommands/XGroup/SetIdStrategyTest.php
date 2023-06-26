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

namespace Predis\Command\Strategy\ContainerCommands\XGroup;

use PredisTestCase;

class SetIdStrategyTest extends PredisTestCase
{
    /**
     * @var SetIdStrategy
     */
    private $strategy;

    protected function setUp(): void
    {
        $this->strategy = new SetIdStrategy();
    }

    /**
     * @dataProvider argumentsProvider
     * @group disconnected
     * @param  array $actualArguments
     * @param  array $expectedResponse
     * @return void
     */
    public function testProcessArguments(array $actualArguments, array $expectedResponse): void
    {
        $this->assertSame($expectedResponse, $this->strategy->processArguments($actualArguments));
    }

    /**
     * @group disconnected
     * @return void
     */
    public function testParseResponse(): void
    {
        $this->assertSame(['arg1', 'arg2'], $this->strategy->parseResponse(['arg1', 'arg2']));
    }

    public function argumentsProvider(): array
    {
        return [
            'with default arguments' => [
                ['SETID', 'key', 'group', '$'],
                ['SETID', 'key', 'group', '$'],
            ],
            'with ENTRIESREAD modifier' => [
                ['SETID', 'key', 'group', '$', 'entry'],
                ['SETID', 'key', 'group', '$', 'ENTRIESREAD', 'entry'],
            ],
        ];
    }
}
