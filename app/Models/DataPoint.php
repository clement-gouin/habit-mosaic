<?php

namespace App\Models;

use Database\Factories\DataPointFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tracker
 *
 * @property int $id
 * @property int $tracker_id
 * @property Carbon $date
 * @property float $value
 * @property-read Tracker $tracker
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static DataPointFactory factory($count = null, $state = [])
 * @method static Builder|DataPoint newModelQuery()
 * @method static Builder|DataPoint newQuery()
 * @method static Builder|DataPoint query()
 *
 * @mixin Eloquent
 */
class DataPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'value',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'value' => 'float',
    ];

    public function tracker(): BelongsTo
    {
        return $this->belongsTo(Tracker::class);
    }

    public function score(): float
    {
        return $this->tracker->scoreFromValue($this->value);
    }
}
