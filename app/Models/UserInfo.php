<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function country(){

        return $this->belongsTo(Country::class);
    }
    
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
