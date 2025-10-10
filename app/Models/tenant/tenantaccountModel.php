<?php

namespace App\Models\tenant;
use Laravel\Sanctum\HasApiTokens;  // Import Sanctum trait
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;


class tenantaccountModel extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $guard = 'tenant';

    protected $table = 'tenants'; 

    protected $primaryKey = 'tenant_id'; 
    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'tenant_id', 
        'firstname',
        'lastname',
        'password',
        'email',
        'phonenumber',
        'gender',
        'city',
        'province',
        'region',
        'postalcode',
        'currentaddress',
        'profilePicUrl',
    ];
    public function getAuthPassword()
    {
        return $this->password;
    }

    protected $hidden = [
        'password', // hide your password field properly
        'remember_token',
    ];
    public function sentNotifications()
{
    return $this->morphMany(Notification::class, 'sender');
}

public function receivedNotifications()
{
    return $this->morphMany(Notification::class, 'receiver');
}


    // If you want to use this model for authentication
    // Make sure you extend Authenticatable instead of Model
}


