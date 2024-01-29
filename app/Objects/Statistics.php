<?php

namespace App\Objects;

use Illuminate\Support\Collection;

readonly class Statistics
{
    private function __construct(
        public float $total,
        public float $min,
        public float $average,
        public float $median,
        public float $max
    ) {
    }

    public static function deserialize(array $data): Statistics
    {
        return new Statistics(
            total: $data['total'],
            min: $data['min'],
            average: $data['average'],
            median: $data['median'],
            max: $data['max'],
        );
    }

    public static function fromDataCollection(Collection $data): Statistics
    {
        return new Statistics(
            total: $data->count(),
            min: $data->min() ?? 0,
            average: $data->average() ?? 0,
            median: $data->median() ?? 0,
            max: $data->max() ?? 0,
        );
    }

    public function serialize(): array
    {
        return [
            'total' => $this->total,
            'min' => $this->min,
            'average' => $this->average,
            'median' => $this->median,
            'max' => $this->max,
        ];
    }
}
