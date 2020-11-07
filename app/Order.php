<?php

namespace App;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
// $table->string('session');
// $table->string('name');
// $table->string('email');
// $table->string('phone');
// $table->string('from_address');
// $table->double('from_location');
// $table->string('to_address')->nullable();
// $table->double('to_address')->nullable();
// $table->unsignedFloat('offer')->nullable();
// $table->integer('status')->default(0);
// $table->unsignedBigInteger('driver_id')->nullable();
// $table->unsignedBigInteger('user_id');
//$table->unsignedBigInteger('parent');
class Order extends Model
{
    // block_drivers=null default,
    //


    // 0 new
    //Office: 1=> S D | 12 => Send Offer
    //Driver: 2 => Y/N option | 21 on the way
    //Customer: 3 => Y/N option
    //Done 9=> done | 91=> RO | 92=>RC | 93=>NoRO | 94=>NoRC
    use Multitenantable;

    protected $fillable = [
        'session',
        'name',
        'email',
        'phone',
        'from_address',
        'from_lat',
        'from_lng',
        'status',
        'user_id',
        'parent'
    ];
    protected $appends = ['driver', 'drivers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return User::find($this->parent);
    }

    public function getDriverAttribute()
    {
        $driver = Driver::find($this->driver_id);
        if (is_object($driver)) {
            return $driver;
        }

        return false;
    }

    public function getDriversAttribute()
    {
        $block = explode('--', $this->block);
        $drivers = Driver::where('user_id', $this->user_id)
            ->where('busy', 0)
            ->whereNotIn('id', $block)
            ->get();

        return $drivers;
    }
}
