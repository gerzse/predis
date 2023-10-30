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

use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommand;

/**
 * @group commands
 * @group realm-zset
 */
class ZCOUNT_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand(): string
    {
        return 'Predis\Command\Redis\ZCOUNT';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId(): string
    {
        return 'ZCOUNT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments(): void
    {
        $arguments = ['key', 0, 10];
        $expected = ['key', 0, 10];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testGetCommandMode(): void
    {
        $command = $this->getCommand();

        $this->assertSame(CommandInterface::READ_MODE, $command->getCommandMode());
    }

    /**
     * @group disconnected
     */
    public function testGetKeys(): void
    {
        $arguments = ['key', 'not_key'];
        $expected = ['key'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getKeys());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse(): void
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys(): void
    {
        /** @var PrefixableCommand $command */
        $command = $this->getCommand();
        $actualArguments = ['arg1', 'arg2', 'arg3', 'arg4'];
        $prefix = 'prefix:';
        $expectedArguments = ['prefix:arg1', 'arg2', 'arg3', 'arg4'];

        $command->setArguments($actualArguments);
        $command->prefixKeys($prefix);

        $this->assertSame($expectedArguments, $command->getArguments());
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testReturnsNumberOfElementsInGivenScoreRange(): void
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(5, $redis->zcount('letters', 0, 100));
        $this->assertSame(5, $redis->zcount('letters', -100, 100));
        $this->assertSame(2, $redis->zcount('letters', 25, 45));
        $this->assertSame(1, $redis->zcount('letters', 20, 20));
        $this->assertSame(0, $redis->zcount('letters', 0, 0));

        $this->assertSame(0, $redis->zcount('unknown', 0, 100));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 6.0.0
     */
    public function testReturnsNumberOfElementsInGivenScoreRangeResp3(): void
    {
        $redis = $this->getResp3Client();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(5, $redis->zcount('letters', 0, 100));
        $this->assertSame(5, $redis->zcount('letters', -100, 100));
        $this->assertSame(2, $redis->zcount('letters', 25, 45));
        $this->assertSame(1, $redis->zcount('letters', 20, 20));
        $this->assertSame(0, $redis->zcount('letters', 0, 0));

        $this->assertSame(0, $redis->zcount('unknown', 0, 100));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testInfinityScoreIntervals(): void
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(3, $redis->zcount('letters', '-inf', 30));
        $this->assertSame(3, $redis->zcount('letters', 30, '+inf'));
        $this->assertSame(5, $redis->zcount('letters', '-inf', '+inf'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testExclusiveScoreIntervals(): void
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 10, 'a', 20, 'b', 30, 'c', 40, 'd', 50, 'e');

        $this->assertSame(2, $redis->zcount('letters', 10, '(30'));
        $this->assertSame(2, $redis->zcount('letters', '(10', 30));
        $this->assertSame(1, $redis->zcount('letters', '(10', '(30'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testThrowsExceptionOnWrongType(): void
    {
        $this->expectException('Predis\Response\ServerException');
        $this->expectExceptionMessage('Operation against a key holding the wrong kind of value');

        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->zcount('foo', 0, 10);
    }
}
