<?php

namespace App\Objects;

use Illuminate\Support\Collection;

readonly class Statistics
{
    private function __construct(
        public int $total = 0,
        public float $min = 0,
        public float $average = 0,
        public float $lowerQuartile = 0,
        public float $median = 0,
        public float $upperQuartile = 0,
        public float $max = 0
    ) {}

    public static function deserialize(array $data): Statistics
    {
        return new Statistics(
            total: $data['total'],
            min: $data['min'],
            average: $data['average'],
            lowerQuartile: $data['lower_quartile'],
            median: $data['median'],
            upperQuartile: $data['upper_quartile'],
            max: $data['max'],
        );
    }

    /**
     * @param  Collection<float>  $data
     */
    public static function fromDataCollection(Collection $data): Statistics
    {
        if ($data->isEmpty()) {
            return new Statistics();
        }

        $median = $data->median() ?? 0;

        return new Statistics(
            total: $data->count(),
            min: $data->min() ?? 0,
            average: $data->average() ?? 0,
            lowerQuartile: $data->where(fn ($v) => $v < $median)->median() ?? 0,
            median: $median,
            upperQuartile: $data->where(fn ($v) => $v > $median)->median() ?? 0,
            max: $data->max() ?? 0,
        );
    }

    public function serialize(): array
    {
        return [
            'total' => $this->total,
            'min' => $this->min,
            'average' => $this->average,
            'lower_quartile' => $this->lowerQuartile,
            'median' => $this->median,
            'upper_quartile' => $this->upperQuartile,
            'max' => $this->max,
        ];
    }
}
