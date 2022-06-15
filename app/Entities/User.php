<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Redis;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = 'api';

    protected $fillable = [
        'company_id',
        'activated',
        'username',
        'full_name',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['is_online'];

    
    public function getIsOnlineAttribute()
    {
      $isOnline = Redis::get('user-is-online-' . $this->id);
      return $isOnline ? true : false;
    }


    public function allPermissions()
    {
        return $this->roles()->with('permissions')->get()->map(function ($item) {
            return $item->permissions->pluck('name');
        })->flatten()->merge($this->permissions->pluck('name'));
    }

    public function company()
    {
      return $this->belongsTo(Company::class, 'company_id');
    }
    public function user_attachment()
    {
      return $this->hasMany(UserAttachment::class);
    }
    public function user_address()
    {
      return $this->hasOne(UserAddress::class);
    }
    public function submission()
    {
      return $this->hasMany(Submission::class,'user_id');
    }
    
    public function user_has_company(){
      return $this->belongsToMany(Company::class, 'user_has_companies', 'user_id', 'company_id');
    }
    public function submission_as_customer()
    {
      return $this->hasMany(Submission::class, 'partner_id');
    }
}

