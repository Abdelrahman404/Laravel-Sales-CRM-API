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

    protected $hidden = [
        'products_interest',

    ];

    protected $appends = ['responsible_seller'];

    public function getResponsibleSellerAttribute(){

        return $this->seller->name ?? 'null' ;
    }
    
    public function country(){

        return $this->belongsTo(Country::class, 'country_id');
    }
    
    public function city(){

        return $this->belongsTo(City::class);
    }
    public function area(){

        return $this->belongsTo(Area::class);
    }

    public function calls(){

        return $this->hasMany(Call::class);
    }

    public function comments(){

        return $this->hasMany(Comment::class);
    }

    public function deals(){

        return $this->hasMany(Deal::class);
    }

    public function case(){

        return $this->belongsTo(Status::class, 'status');
    }  
    
    public function products(){

        return $this->belongsToMany(Product::class, 'clients_products', 'client_id', 'product_id');
    }
    
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function seller(){

        return $this->belongsTo(User::class , 'responsible_seller_id');
    }

    public function wayFoundClient()
    {
        return $this->belongsTo(WayFoundClient::class, 'way_found_client_id');
    }


    
}
