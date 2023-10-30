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

use Predis\Command\PrefixableCommand;

/**
 * @group commands
 * @group realm-string
 */
class GETSET_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand(): string
    {
        return 'Predis\Command\Redis\GETSET';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId(): string
    {
        return 'GETSET';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments(): void
    {
        $arguments = ['key', 'value'];
        $expected = ['key', 'value'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
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
        $this->assertSame('value', $this->getCommand()->parseResponse('value'));
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
     */
    public function testReturnsPreviousValueOfKey(): void
    {
        $redis = $this->getClient();

        $this->assertNull($redis->getset('foo', 'bar'));
        $this->assertSame('bar', $redis->getset('foo', 'barbar'));

        $redis->set('hoge', 'piyo');
        $this->assertSame('piyo', $redis->getset('hoge', 'piyopiyo'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 6.0.0
     */
    public function testReturnsPreviousValueOfKeyResp3(): void
    {
        $redis = $this->getResp3Client();

        $this->assertNull($redis->getset('foo', 'bar'));
        $this->assertSame('bar', $redis->getset('foo', 'barbar'));

        $redis->set('hoge', 'piyo');
        $this->assertSame('piyo', $redis->getset('hoge', 'piyopiyo'));
    }

    /**
     * @group connected
     */
    public function testThrowsExceptionOnWrongType(): void
    {
        $this->expectException('Predis\Response\ServerException');
        $this->expectExceptionMessage('Operation against a key holding the wrong kind of value');

        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $redis->getset('metavars', 'foo');
    }
}
