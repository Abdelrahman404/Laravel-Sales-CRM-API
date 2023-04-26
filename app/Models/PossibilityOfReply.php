<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossibilityOfReply extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['name_en', 'name_ar', 'created_at', 'updated_at'];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->{'name_'.app()->getLocale()};
    }

    public function calls(){

        return $this->hasMany(Call::class, 'possibility_reply_id');
    }


}
