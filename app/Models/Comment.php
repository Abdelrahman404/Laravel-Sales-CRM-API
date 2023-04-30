<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['created_by'];

    // To always get records from newest to oldest.
    protected static function booted()
    {
        static::addGlobalScope('latest', function (Builder $builder) {
            $builder->latest('created_at');
        });
    }
    
    public function getCreatedByAttribute()
    {
        return $this->user->name;
    }


    public function user(){

        return $this->belongsTo(User::class);
    }

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
