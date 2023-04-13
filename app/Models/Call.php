<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function possibilityOfReply(){

        return $this->belongsTo(PossibilityOfReply::class);
    }
  
    public function client(){

        return $this->belongsTo(Client::class);
    }

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
