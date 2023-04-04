<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\Country;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function country(){

        return $this->belongsTo(Country::class, 'country_id');
    }
    
    public function city(){

        return $this->belongsTo(City::class);
    }
    public function area(){

        return $this->belongsTo(Area::class);
    }
    
}
