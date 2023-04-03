<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'name_ar',
        'name_en',
    ];
    
    protected $appends = ['name'];
    
    public function getNameAttribute()
    {
        return $this->{'name_'.app()->getLocale()};
    }

    public function cities(){

        return $this->hasMany(City::class);
    }


}
