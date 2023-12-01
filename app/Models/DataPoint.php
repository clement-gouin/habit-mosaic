<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Database\Factories\TrackerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tracker
 *
 * @property int $id
 * @property int $tracker_id
 * @property Carbon $date
 * @property float $value
 * @property-read Tracker $tracker
 * @method static DataPoint factory($count = null, $state = [])
 * @method static Builder|DataPoint newModelQuery()
 * @method static Builder|DataPoint newQuery()
 * @method static Builder|DataPoint query()
 * @mixin Eloquent
 */
class DataPoint extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function tracker(): BelongsTo
    {
        return $this->belongsTo(Tracker::class);
    }
}
