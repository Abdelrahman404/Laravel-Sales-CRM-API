<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossibilityOfReply extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function calls(){

        return $this->hasMany(Call::class);
    }


}
