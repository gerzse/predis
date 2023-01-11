<?php

namespace Predis\Command\Redis\Json;

use Predis\Command\Redis\PredisCommandTestCase;

class JSONARRTRIM_Test extends PredisCommandTestCase
{
    /**
     * @inheritDoc
     */
    protected function getExpectedCommand(): string
    {
        return JSONARRTRIM::class;
    }

    /**
     * @inheritDoc
     */
    protected function getExpectedId(): string
    {
        return 'JSONARRTRIM';
    }


    /**
     * @group disconnected
     */
    public function testFilterArguments(): void
    {
        $arguments = ['key', '$..', 0, 1];
        $expected = ['key', '$..', 0, 1];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse(): void
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group connected
     * @dataProvider jsonProvider
     * @param array $jsonArguments
     * @param string $key
     * @param string $path
     * @param int $start
     * @param int $stop
     * @param array $expectedArrayLength
     * @param string $expectedModifiedJson
     * @return void
     * @requiresRedisJsonVersion >= 1.0.0
     */
    public function testCorrectlyTrimGivenJsonArray(
        array $jsonArguments,
        string $key,
        string $path,
        int $start,
        int $stop,
        array $expectedArrayLength,
        string $expectedModifiedJson
    ): void {
        $redis = $this->getClient();

        $redis->jsonset(...$jsonArguments);
        $actualResponse = $redis->jsonarrtrim($key, $path, $start, $stop);

        $this->assertSame($expectedArrayLength, $actualResponse);
        $this->assertSame($expectedModifiedJson, $redis->jsonget($key));
    }

    public function jsonProvider(): array
    {
        return [
            'trim array from start to stop' => [
                ['key', '$', '{"key1":"value1","key2":[1,2,3,4,5,6]}'],
                'key',
                '$.key2',
                1,
                4,
                [4],
                '{"key1":"value1","key2":[2,3,4,5]}'
            ],
            'trim all values except first with 0 start and stop' => [
                ['key', '$', '{"key1":"value1","key2":[1,2,3,4,5,6]}'],
                'key',
                '$.key2',
                0,
                0,
                [1],
                '{"key1":"value1","key2":[1]}'
            ],
            'trim arrays with same keys on root and nested levels' => [
                ['key', '$', '{"key1":{"key2":[1,2,3,4,5,6]},"key2":[1,2,3,4,5,6]}'],
                'key',
                '$..key2',
                1,
                4,
                [4,4],
                '{"key1":{"key2":[2,3,4,5]},"key2":[2,3,4,5]}'
            ],
            'do not trim on non-array key' => [
                ['key', '$', '{"key1":"value1","key2":"value2"}'],
                'key',
                '$.key2',
                1,
                4,
                [null],
                '{"key1":"value1","key2":"value2"}'
            ],
        ];
    }
}
