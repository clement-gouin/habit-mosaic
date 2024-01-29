<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $icon
 * @property int $order
 * @property-read User $user
 * @property-read Collection|Tracker[] $trackers
 * @property-read Collection|DataPoint[] $dataPoints
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static CategoryFactory factory($count = null, $state = [])
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 *
 * @mixin Eloquent
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
        'order',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trackers(): HasMany
    {
        return $this->hasMany(Tracker::class)->orderByDesc('order');
    }

    public function dataPoints(): HasManyThrough
    {
        return $this->hasManyThrough(DataPoint::class, Tracker::class);
    }
}
