<?php

namespace Tests\Unit\Lottery\WinningNumber\Sources;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Lottery\Lottery;
use App\Lottery\WinningNumber\Sources\SourceOne;

class SourceOneTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFetch()
    {
        $issue = '20200606';
        $expectedResult = "0,6,2,2,3";

        $mockedResponse = $this->mock(Response::class, function ($mock) use ($issue, $expectedResult) {
            $mock->shouldReceive('getContent')
                ->once()
                ->andReturn(json_encode([
                    "result" => [
                        "cache" => 0,
                        "data" => [
                            [
                                "gid" => $issue,
                                "award" => $expectedResult,
                                "updatetime" => "1567446006"
                            ]
                        ]
                    ],
                    "errorCode" => 0
                ]));
        });

        $this->mock(Client::class, function ($mock) use ($issue, $mockedResponse) {
            $mock->shouldReceive('get')
                ->once()
                ->with("http://one.fake/v1?gamekey=" . SourceOne::GAME_KEY_MAP[Lottery::CQSSC_GAME_ID] . "&issue=$issue")
                ->andReturn($mockedResponse);
        });

        $sourceOne = app()->make(SourceOne::class);
        $result = $sourceOne->fetch(Lottery::CQSSC_GAME_ID, $issue);

        $this->assertEquals($result, $expectedResult);
    }
}
