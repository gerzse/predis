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

namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\Redis\PredisCommandTestCase;
use Predis\Response\ServerException;

class CFDEL_Test extends PredisCommandTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function getExpectedCommand(): string
    {
        return CFDEL::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getExpectedId(): string
    {
        return 'CFDEL';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments(): void
    {
        $actualArguments = ['key', 'item'];
        $expectedArguments = ['key', 'item'];

        $command = $this->getCommand();
        $command->setArguments($actualArguments);

        $this->assertSameValues($expectedArguments, $command->getArguments());
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
     * @return void
     * @requiresRedisVersion >= 7.0.0
     */
    public function testDeletesItemFromGivenCuckooFilter(): void
    {
        $redis = $this->getClient();

        $redis->cfAdd('key', 'item');
        $singleItemResponse = $redis->cfDel('key', 'item');

        $this->assertSame(1, $singleItemResponse);
        $this->assertSame(0, $redis->cfExists('key', 'item'));

        $redis->cfAdd('key', 'item');
        $redis->cfAdd('key', 'item');

        $multipleItemsResponse = $redis->cfDel('key', 'item');
        $this->assertSame(1, $multipleItemsResponse);
        $this->assertSame(1, $redis->cfExists('key', 'item'));

        $nonExistingItemResponse = $redis->cfDel('key', 'non_existing_item');
        $this->assertSame(0, $nonExistingItemResponse);
    }

    /**
     * @group connected
     * @return void
     * @requiresRedisVersion >= 7.0.0
     */
    public function testDeleteThrowsExceptionOnNonExistingFilterKey(): void
    {
        $redis = $this->getClient();

        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Not found');

        $redis->cfDel('non_existing_key', 'item');
    }
}
