<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Entities\Keyfigure
 *
 * @property int $id
 * @property int $portfolio_id
 * @property int $key_id
 * @property int $date_id
 * @property float $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Entities\KeyfigureDate $date
 * @property-read \App\Entities\KeyfigureType $key
 * @property-read \App\Entities\Portfolio $portfolio
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereDateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereKeyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure wherePortfolioId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Keyfigure whereValue($value)
 * @mixin \Eloquent
 * @property int $type_id
 * @property array $values
 * @property string|null $expires_at
 * @property-read \App\Entities\KeyfigureType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Keyfigure whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Keyfigure whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Keyfigure whereValues($value)
 */
class Keyfigure extends Model
{

    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'values',
        'effective_at'
    ];

    protected $casts = [
        'values' => 'json'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'effective_at'
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function type()
    {
        return $this->belongsTo(KeyfigureType::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */


    public function get($key)
    {
        return array_get($this->values, $key);
    }

    public function set($key, $value)
    {
        $values = $this->values;
        $values[$key] = $value;

        $this->update(['values' => $values]);
    }

    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

    private function hasValues()
    {
        return count($this->values) > 0;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($keyfigures) {
            $keyfigures->values = [];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOfType($query, $type)
    {
        return $query->whereHas('type', function ($query) use ($type) {
            $query->whereCode($type);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getValueAttribute()
    {
        return $this->values ? array_last($this->values) : [];
    }

    public function getDateAttribute()
    {
        return $this->values ? Carbon::parse(array_last(array_keys($this->values))) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
