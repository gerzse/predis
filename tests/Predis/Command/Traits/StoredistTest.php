<?php

namespace Predis\Command\Traits;

use Predis\Command\Command as RedisCommand;
use PredisTestCase;
use UnexpectedValueException;

class StoredistTest extends PredisTestCase
{
    private $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testClass = new class extends RedisCommand {
            use Storedist;

            public static $storeDistArgumentPositionOffset = 0;

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
        $this->testClass::$storeDistArgumentPositionOffset = $offset;

        $this->testClass->setArguments($actualArguments);

        $this->assertSame($expectedArguments, $this->testClass->getArguments());
    }

    /**
     * @return void
     */
    public function testThrowsExceptionOnUnexpectedValue(): void
    {
        $this->testClass::$storeDistArgumentPositionOffset = 0;

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Wrong STOREDIST argument type");

        $this->testClass->setArguments(['test']);
    }

    public function argumentsProvider(): array
    {
        return [
            'STOREDIST false argument' => [
                0,
                [false, 'second argument', 'third argument'],
                [false, 'second argument', 'third argument']
            ],
            'STOREDIST argument first and there is arguments after' => [
                0,
                [true, 'second argument', 'third argument'],
                ['STOREDIST', 'second argument', 'third argument']
            ],
            'STOREDIST argument last and there is arguments before' => [
                2,
                ['first argument', 'second argument', true],
                ['first argument', 'second argument', 'STOREDIST']
            ],
            'STOREDIST argument not the first and not the last' => [
                1,
                ['first argument', true, 'third argument'],
                ['first argument', 'STOREDIST', 'third argument']
            ],
        ];
    }
}
