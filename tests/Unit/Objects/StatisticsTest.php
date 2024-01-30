<?php

namespace Tests\Unit\Objects;

use App\Objects\Statistics;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    /** @test */
    public function it_calculate_statistics_from_empty_collection(): void
    {
        $statistics = Statistics::fromDataCollection(collect());

        $this->assertEquals([
            'total' => 0,
            'min' => 0,
            'average' => 0,
            'lower_quartile' => 0,
            'median' => 0,
            'upper_quartile' => 0,
            'max' => 0,
        ], $statistics->serialize());
    }

    /** @test */
    public function it_calculate_statistics_from_data(): void
    {
        $statistics = Statistics::fromDataCollection(collect([1, 9, 2, 8, 3, 7, 4, 6, 5, 0]));

        $this->assertEquals([
            'total' => 10,
            'min' => 0,
            'average' => 4.5,
            'lower_quartile' => 2,
            'median' => 4.5,
            'upper_quartile' => 7,
            'max' => 9,
        ], $statistics->serialize());
    }
}
