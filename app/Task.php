<?php

namespace App;

use App\Nova\User;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->user_id = auth()->user()->id;
        });
    }

    public function tasks()
    {
        return $this->belongsTo(User::class);
    }
}
