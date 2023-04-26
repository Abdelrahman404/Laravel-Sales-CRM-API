<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Call extends Model
{
    use HasFactory;

    protected $guarded = [];


    // To always get records from newest to oldest.
    protected static function booted()
    {
        static::addGlobalScope('latest', function (Builder $builder) {
            $builder->latest('created_at');
        });
    }

    public function possibilityOfReply(){

        return $this->belongsTo(PossibilityOfReply::class, 'possibility_reply_id');
    }
  
    public function client(){

        return $this->belongsTo(Client::class);
    }

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
