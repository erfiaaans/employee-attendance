<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'attendance_id',
        'user_id',
        'location_id',
        'clock_in_time',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_in_photo_url',
        'clock_out_time',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_out_photo_url',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
