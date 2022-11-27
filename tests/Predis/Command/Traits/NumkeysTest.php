<?php

namespace Predis\Command\Traits;

use PredisTestCase;
use Predis\Command\Command as RedisCommand;
use UnexpectedValueException;

class NumkeysTest extends PredisTestCase
{
    private $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testClass = new class extends RedisCommand {
            use Numkeys;

            public static $keysArgumentPositionOffset = 0;

            public function getId()
            {
                return 'test';
            }
        };
    }

    /**
     * @dataProvider argumentsProvider
     * @param int $offset
     * @param array $actualArguments
     * @param array $expectedArguments
     * @return void
     */
    public function testReturnsCorrectArguments(int $offset, array $actualArguments, array $expectedArguments): void
    {
        $this->testClass::$keysArgumentPositionOffset = $offset;

        $this->testClass->setArguments($actualArguments);

        $this->assertSame($expectedArguments, $this->testClass->getArguments());
    }

    /**
     * @dataProvider unexpectedValuesProvider
     * @param int $offset
     * @param array $actualArguments
     * @return void
     */
    public function testThrowsExceptionOnUnexpectedValueGiven(int $offset, array $actualArguments): void
    {
        $this->testClass::$keysArgumentPositionOffset = $offset;

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Wrong keys argument type or position offset');

        $this->testClass->setArguments($actualArguments);
    }

    public function argumentsProvider(): array
    {
        return [
            'keys argument first and there is arguments after' => [
                0,
                [['key1', 'key2'], 'second argument', 'third argument'],
                [2, ['key1', 'key2'], 'second argument', 'third argument']
            ],
            'keys argument last and there is arguments before' => [
                2,
                ['first argument', 'second argument', ['key1', 'key2']],
                ['first argument', 'second argument', 2, ['key1', 'key2']]
            ],
            'keys argument not the first and not the last' => [
                1,
                ['first argument', ['key1', 'key2'], 'third argument'],
                ['first argument', 2, ['key1', 'key2'], 'third argument']
            ],
            'keys argument the only argument' => [
                0,
                [['key1', 'key2']],
                [2, ['key1', 'key2']]
            ]
        ];
    }

    public function unexpectedValuesProvider(): array
    {
        return [
            'keys argument not an array' => [
                0,
                ['key1'],
            ],
            'keys argument position offset higher then arguments quantity' => [
                2,
                [['key1', 'key2']],
            ]
        ];
    }
}
