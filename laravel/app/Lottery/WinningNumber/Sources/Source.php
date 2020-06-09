<?php
namespace App\Lottery\WinningNumber\Sources;

use GuzzleHttp\Client;

abstract class Source
{
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetch(int $gameId, string $issue)
    {
        //
    }
}