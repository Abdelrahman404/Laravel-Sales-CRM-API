<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';
    
    protected $guarded = [];

    protected $hidden = [
        'name_ar',
        'name_en',
        'created_at',
        'updated_at',
    ];
    
    protected $appends = ['name'];
    
    public function getNameAttribute()
    {
        return $this->{'name_'.app()->getLocale()};
    }

    public function cities(){

        return $this->hasMany(City::class);
    }

    public function clients(){

        return $this->hasMany(Client::class);
    }


}
