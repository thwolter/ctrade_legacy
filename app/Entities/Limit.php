<?php

namespace App\Entities;

use App\Presenters\LimitPresenter;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Entities\Limit
 *
 * @property int $id
 * @property int $portfolio_id
 * @property int $type_id
 * @property float $limit
 * @property string $date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Portfolio $portfolio
 * @property-read \App\Entities\LimitType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Limit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Limit extends Model
{
    use Presentable, SoftDeletes;

    protected $presenter = LimitPresenter::class;

    protected $fillable = [
        'type',
        'value',
        'date',
        'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }


    public function type() {
        return $this->belongsTo(LimitType::class)->withDefault();
    }


    public function toArray()
    {
        return $this->getAttributes();
    }


    public function scopeActive($query)
    {
        return $query->whereActive(true);
    }


    public function scopeFinite($query)
    {
        return $query->where('date', '!=', null);
    }

    public function scopeInfinite($query)
    {
        return $query->where('date', null);
    }

    public function scopeOfType($query, $type)
    {
        return $query->whereHas('type', function ($query) use ($type) {$query->whereCode($type);});
    }
}
