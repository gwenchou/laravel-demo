<?php

namespace Tests\Unit\Lottery\WinningNumber\Sources;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Lottery\Lottery;
use App\Lottery\WinningNumber\Sources\SourceTwo;

class SourceTwoTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFetch()
    {
        $issue = '20190902002';
        $expectedResult = "3,1,5,8,6";

        $mockedResponse = $this->mock(Response::class, function ($mock) use ($issue, $expectedResult) {
            $mock->shouldReceive('getContent')
                ->once()
                ->andReturn(json_encode([
                    "rows" => 3,
                    "code" => SourceTwo::GAME_KEY_MAP[Lottery::BJ11X5_GAME_ID],
                    "data" => [
                      [
                        "expect" => "20190902003",
                        "opencode" => "3,8,1,9,5",
                        "opentime" => "2019-09-02 01:12:46"
                      ],
                      [
                        "expect" => $issue,
                        "opencode" => $expectedResult,
                        "opentime" => "2019-09-02 00:52:37"
                      ],
                      [
                        "expect" => "20190902001",
                        "opencode" => "6,1,9,0,3",
                        "opentime" => "2019-09-02 00:32:03"
                      ],
                    ]
                ]));
        });

        $this->mock(Client::class, function ($mock) use ($issue, $mockedResponse) {
            $mock->shouldReceive('get')
                ->once()
                ->with("https://two.fake/newly.do?code=" . SourceTwo::GAME_KEY_MAP[Lottery::BJ11X5_GAME_ID])
                ->andReturn($mockedResponse);
        });

        $sourceTwo = app()->make(SourceTwo::class);
        $result = $sourceTwo->fetch(Lottery::BJ11X5_GAME_ID, $issue);

        $this->assertEquals($result, $expectedResult);
    }
}
