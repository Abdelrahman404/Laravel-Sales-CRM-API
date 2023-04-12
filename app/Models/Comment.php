<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['created_by'];
    
    public function getCreatedByAttribute()
    {
        return $this->user->name;
    }


    public function user(){

        return $this->belongsTo(User::class);
    }
    
}
