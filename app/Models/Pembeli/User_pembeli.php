<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Pembeli\Chat_pembeli;
use App\Models\Pembeli\UserAddress;

class User_pembeli extends Authenticatable
{
    protected $table = 'users';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function productRatings()
    {
        return $this->hasMany(ProductRating_pembeli::class, 'user_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental_pembeli::class, 'user_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat_pembeli::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order_pembeli::class, 'user_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function addressRelation()
    {
        return $this->hasOne(UserAddress::class, 'user_id');
    }

    public function getAddressAttribute()
    {
        return $this->addressRelation?->alamat;
    }

    public function getCityAttribute()
    {
        return $this->addressRelation?->kota;
    }

    public function getPostalCodeAttribute()
    {
        return $this->addressRelation?->kode_pos;
    }

    public function setAddressAttribute($value)
    {
        if ($this->exists) {
            $this->addressRelation()->updateOrCreate(
                ['user_id' => $this->id],
                ['alamat' => $value]
            );
        }
    }

    public function setCityAttribute($value)
    {
        if ($this->exists) {
            $this->addressRelation()->updateOrCreate(
                ['user_id' => $this->id],
                ['kota' => $value]
            );
        }
    }

    public function setPostalCodeAttribute($value)
    {
        if ($this->exists) {
            $this->addressRelation()->updateOrCreate(
                ['user_id' => $this->id],
                ['kode_pos' => $value]
            );
        }
    }
}
