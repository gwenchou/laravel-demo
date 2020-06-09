<?php
use App\Lottery\Lottery;
use App\Lottery\WinningNumber\Sources\SourceOne;
use App\Lottery\WinningNumber\Sources\SourceTwo;

return [
    'sources' => [
        SourceOne::class,
        SourceTwo::class,
    ],

    'main_source_lotteries' => [
        Lottery::CQSSC_GAME_ID => SourceOne::class,
        Lottery::BJ11X5_GAME_ID => SourceTwo::class,
    ]
];