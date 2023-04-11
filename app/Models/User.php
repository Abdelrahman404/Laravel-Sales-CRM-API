<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'email',
        'password',
        'image',
        'type'
    ];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->{'name_'.app()->getLocale()};
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'name_ar',
        'name_en',
        'remember_token',
        'email_verified_at',
        'updated_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function userInfo(){

        return $this->hasMany(UserInfo::class);
    }

    public function comments(){

        return $this->hasMany(Comment::class);
    }

    public function deals(){

        return $this->hasMany(Deal::class);
    }
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}