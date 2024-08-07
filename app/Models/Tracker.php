<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\TrackerFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Tracker
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $icon
 * @property int $order
 * @property string|null $unit
 * @property float $value_step
 * @property float $target_value
 * @property float $target_score
 * @property bool $single
 * @property bool $overflow
 * @property int|null $stale_delay
 * @property-read User $user
 * @property-read Category $category
 * @property-read Collection<DataPoint> $dataPoints
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static TrackerFactory factory($count = null, $state = [])
 * @method static Builder|Tracker newModelQuery()
 * @method static Builder|Tracker newQuery()
 * @method static Builder|Tracker query()
 *
 * @mixin Eloquent
 */
class Tracker extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'icon',
        'order',
        'unit',
        'value_step',
        'target_value',
        'target_score',
        'single',
        'overflow',
        'stale_delay',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'value_step' => 'float',
        'target_value' => 'float',
        'target_score' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getUserAttribute(): User
    {
        return $this->category->user;
    }

    public function dataPoints(): HasMany
    {
        return $this->hasMany(DataPoint::class)->orderByDesc('date');
    }

    public function scoreFromValue(float $value): float
    {
        return $this->target_score * $value / $this->target_value;
    }

    public function valueFromScore(float $score): float
    {
        return $this->target_value * $score / $this->target_score;
    }

    public function getDataPointAt(Carbon $date): DataPoint|Model
    {
        return $this->dataPoints()
            ->firstOrCreate([
                'date' => $date->startOfDay(),
            ])->refresh();
    }

    public function getLastDataPoint(): DataPoint|Model|null
    {
        return $this->dataPoints()
            ->where('data_points.value', '!=', 0)
            ->first();
    }

    public function getScoreAt(CarbonInterface $date): float
    {
        $value = $this->dataPoints()->firstWhere('date', $date->startOfDay())?->value ?? 0;

        return $this->scoreFromValue($value);
    }
}
