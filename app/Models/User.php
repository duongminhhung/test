<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Session;
use DateTimeInterface;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    // protected $connection = 'sqlsrv';

    /**     
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $primaryKey = 'id';
    protected $table      = 'users';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'cache'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function role()
    {
        return $this->belongsToMany('App\Models\Role', 'user_role');
    }

    public function checkRole($role)
    {
        if(Auth::check())
        {
            if(Auth::user()->level == 9999) return true;
        }
        if (Session::has('roles')) {
            $find = array_search($role, Session::get('roles'));
            if ($find !== false) return true;
        } else {
            if ($this->role()->where('role', $role)->count() == 1) {
                return true;
            }
        }
        return false;
    }
}
