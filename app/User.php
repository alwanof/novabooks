<?php

namespace App;

use App\Traits\Multitenantable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silvanite\Brandenburg\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use Multitenantable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'avatar', 'email', 'password', 'level', 'ref', 'active'
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($user) {
    //         $user->ref = auth()->user()->id;
    //     });
    // }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getParentAttribute()
    {

        return User::withoutGlobalScope('ref')->find($this->ref);
    }

    // public function drivers()
    // {
    //     $level = $this->level;
    //     switch ($level) {
    //         case 1:
    //             return $this->hasMany(Driver::class, 'parent');
    //             break;

    //         default:
    //             return $this->hasMany(Driver::class, 'user_id');
    //             break;
    //     }
    // }
}
