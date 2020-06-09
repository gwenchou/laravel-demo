<?php
namespace App\Lottery\WinningNumber\Sources;

use Illuminate\Support\Arr;
use App\Lottery\Lottery;

class SourceOne extends Source
{
    const GAME_KEY_MAP = [
        Lottery::CQSSC_GAME_ID => 'ssc',
        Lottery::BJ11X5_GAME_ID => 'bjsyxw',
    ];

    public function fetch($gameId, $issue)
    {
        try {
            $response = $this->httpClient->get(
                "http://one.fake/v1?gamekey=" . self::GAME_KEY_MAP[$gameId] . "&issue=$issue",
            );
    
            if (! $winningNumber = $this->resolveWinningNumber($response->getContent(), $issue)) {
                throw \Exception("Could not get winning number from SourceOne API response. gameId:$gameId, issue:$issue");
            };
    
            return $winningNumber;
        } catch (\Exception $e) {
            \Log::error($e);

            return null;
        }
    }

    protected function resolveWinningNumber($content, $issue)
    {
        $decodedContent = json_decode($content, true);

        $data = collect(Arr::get($decodedContent, 'result.data'));

        $interestedRecord = $data->firstWhere('gid', $issue);

        return $interestedRecord['award'];
    }
}
