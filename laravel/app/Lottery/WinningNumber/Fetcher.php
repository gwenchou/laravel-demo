<?php
namespace App\Lottery\WinningNumber;

use Illuminate\Support\Collection;
use App\Lottery\Lottery;
use App\Lottery\WinningNumber\Sources\Source;
use App\Lottery\WinningNumber\Sources\SourceOne;
use App\Lottery\WinningNumber\Sources\SourceTwo;

class Fetcher
{
    protected $sources;
    protected $lottery;
    protected $mainSourceMap;

    public function __construct(Lottery $lottery)
    {
        $this->lottery = $lottery;
    }

    public function getWinningNumber()
    {
        $mainSource = $this->getMainSource();     //主號源
        $otherSources = $this->getOtherSources(); //副號源s

        if (! $numberFromMainSource = $mainSource->fetch($this->lottery->gameId, $this->lottery->issue)) {
            throw new FetchFailureException('Could not get winning number from main source API response');
        }

        $matched = false;
        $otherSources->each(function ($source) use ($numberFromMainSource, &$matched) {
            $numberFromOtherSource = app()->make($source)->fetch($this->lottery->gameId, $this->lottery->issue);

            if ($numberFromMainSource === $numberFromOtherSource) {
                $matched = true;
                return false;
            }
        });

        if (! $matched) {
            throw new FetchFailureException('Got winning number from main source, but could not match it from other sources');
        }

        return $numberFromMainSource;
    }

    public function getMainSource(): Source
    {
        return app()->make(config('lottery.main_source_lotteries')[$this->lottery->gameId]);
    }

    public function getOtherSources(): Collection
    {
        return collect(config('lottery.sources'))->reject(function ($value) {
                return $value == config('lottery.main_source_lotteries')[$this->lottery->gameId];
            })
            ->values();
    }
}
