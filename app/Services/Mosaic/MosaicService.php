<?php

namespace App\Services\Mosaic;

use App\Objects\Statistics;
use App\Services\Service;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @template T
 */
abstract class MosaicService extends Service
{
    /**
     * @param  T  $value
     * @return array<int, float|null>
     */
    public function getAllMosaicData($value): array
    {
        $maxDate = $this->getMaxDate($value);
        /** @var Collection<float|null> $result */
        $result = collect();
        $date = Carbon::today()->startOfWeek();
        while (floor($date->diffInWeeks($maxDate)) <= 0) {
            $result->push(...$this->getWeekData($value, $date));
            $date = $date->subWeek();
        }

        return $result->toArray();
    }

    /**
     * @param  T  $value
     * @return array<int, float|null>
     */
    public function getMosaicData($value, int $days): array
    {
        /** @var Collection<float|null> $result */
        $result = collect();
        $date = Carbon::today()->startOfWeek();
        for ($i = 0; $i < $days / 7; $i++) {
            $result->push(...$this->getWeekData($value, $date));
            $date = $date->subWeek();
        }

        return $result->toArray();
    }

    /**
     * @param  Collection<T>  $values
     * @return array<int, float|null>
     */
    protected function getCollectionWeekData(Collection $values, CarbonInterface $startDate): array
    {
        $collectionData = $values->map(fn ($value) => $this->getWeekData($value, $startDate));
        $firstRow = $collectionData->first() ?? [null, null, null, null, null, null, null];
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $data[] = $firstRow[$i] === null ? null : $collectionData->sum(fn (array $d) => $d[$i]);
        }

        return $data;
    }

    /**
     * @param  T  $value
     * @return array<int, float|null>
     */
    protected function getWeekData($value, CarbonInterface $startDate): array
    {
        if ($startDate->isSameWeek(Carbon::today())) {
            return Cache::remember(
                $this->getCacheKey($value, $startDate),
                5 * 60,
                fn () => $this->computeWeekData($value, $startDate)
            );
        }

        return Cache::rememberForever(
            $this->getCacheKey($value, $startDate),
            fn () => $this->computeWeekData($value, $startDate)
        );
    }

    /**
     * @param  T  $value
     */
    public function clearData($value, CarbonInterface $date): void
    {
        Cache::forget($this->getCacheKey($value, $date));
    }

    /**
     * @param  T  $value
     */
    protected function getCacheKey($value, CarbonInterface $date): string
    {
        $rootCacheKey = $this->getRootCacheKey($value);

        /** @var ?CarbonInterface $maxDate */
        $maxDate = Cache::get($rootCacheKey.'.max');

        if (! $maxDate || $date->isBefore($maxDate)) {
            Cache::put($rootCacheKey.'.max', $date);
        }

        return $this->getRootCacheKey($value).'.'.$date->year.'.'.$date->week;
    }

    /**
     * @param  T  $value
     */
    public function getStatistics($value): Statistics
    {
        return Statistics::deserialize(Cache::remember(
            $this->getRootCacheKey($value).'.statistics',
            Carbon::today()->addDay(),
            function () use ($value) {
                /** @var Collection<float> $data */
                $data = collect($this->getAllMosaicData($value))->filter(fn (?float $v) => $v !== null);

                return Statistics::fromDataCollection($data)->serialize();
            }
        ));
    }

    /**
     * @param  T  $value
     * @return array<int, float|null>
     */
    abstract protected function computeWeekData($value, CarbonInterface $startDate): array;

    /**
     * @param  T  $value
     */
    abstract protected static function getRootCacheKey($value): string;

    /** @param T $value */
    abstract protected function getMaxDate($value): ?CarbonInterface;

    /**
     * @param  T  $value
     */
    public function wipeData($value): void
    {
        $rootCacheKey = $this->getRootCacheKey($value);

        /** @var ?CarbonInterface $date */
        $date = Cache::get($rootCacheKey.'.max');

        if ($date) {
            $max = Carbon::today()->addDay();

            while ($max->isAfter($date)) {
                $this->clearData($value, $date);

                $date = $date->addWeek();
            }

            Cache::forget($rootCacheKey.'.max');
        }

        Cache::forget($rootCacheKey.'.statistics');
    }
}
