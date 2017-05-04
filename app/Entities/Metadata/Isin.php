<?php

namespace App\Entities\Metadata;

use Illuminate\Database\Eloquent\Model;

class Isin extends Model
{
    protected $fillable = [
        'isin'
    ];
    
    public function metadata()
    {
        return $this->belongsTo('App\Entities\Metadata\Metadata');
    }
}