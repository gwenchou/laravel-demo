<?php
namespace App\Lottery\WinningNumber\Sources;

use Illuminate\Support\Arr;
use App\Lottery\Lottery;

class SourceTwo extends Source
{
    const GAME_KEY_MAP = [
        Lottery::CQSSC_GAME_ID => 'cqssc',
        Lottery::BJ11X5_GAME_ID => 'bj11x5',
    ];

    public function fetch($gameId, $issue)
    {
        try {
            $response = $this->httpClient->get(
                "https://two.fake/newly.do?code=" . self::GAME_KEY_MAP[$gameId],
            );
    
            if (! $winningNumber = $this->resolveWinningNumber($response->getContent(), $issue)) {
                throw \Exception("Could not get winning number from SourceTwo API response. gameId:$gameId, issue:$issue");
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

        $data = collect(Arr::get($decodedContent, 'data'));

        $interestedRecord = $data->firstWhere('expect', $issue);

        return $interestedRecord['opencode'];
    }
}
