<?php

namespace Tests\Unit\Lottery\WinningNumber;

use Tests\TestCase;
use App\Lottery\Lottery;
use App\Lottery\WinningNumber\Fetcher;
use App\Lottery\WinningNumber\Sources\SourceOne;
use App\Lottery\WinningNumber\Sources\SourceTwo;

class FetcherTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetWinningNumber()
    {
        $gameId = Lottery::BJ11X5_GAME_ID;
        $issue = '20200606';
        $expectedWinningNumber = '8,5,6,2,4';

        $this->mock(SourceOne::class, function ($mock) use ($gameId, $issue, $expectedWinningNumber) {
            $mock->shouldReceive('fetch')
                ->with($gameId, $issue)
                ->andReturn($expectedWinningNumber);
        });

        $this->mock(SourceTwo::class, function ($mock) use ($gameId, $issue, $expectedWinningNumber) {
            $mock->shouldReceive('fetch')
                ->with($gameId, $issue)
                ->andReturn($expectedWinningNumber);
        });

        $fetcher = new Fetcher(new Lottery($gameId, $issue));
        $winningNumber = $fetcher->getWinningNumber();

        $this->assertEquals($winningNumber, $expectedWinningNumber);
    }
}
