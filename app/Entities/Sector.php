<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entities\Sector
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Stock[] $stocks
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Sector whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Sector whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Sector whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entities\Sector whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sector extends Model
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name'
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function getEnumValuesAsAssociativeArray($field_name)
    {
        $array = [];
        foreach (self::all() as $item)
        {
            $array[$item->id] = $item->$field_name;
        }
        return $array;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
