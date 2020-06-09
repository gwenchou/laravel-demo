<?php
namespace App\Lottery;

class Lottery
{
    const CQSSC_GAME_ID = 1;
    const BJ11X5_GAME_ID = 2;

    public $gameId;
    public $issue;

    public function __construct($gameId, $issue)
    {
        $this->gameId = $gameId;
        $this->issue = $issue;
    }
}
