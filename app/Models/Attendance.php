<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
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
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function getClockInPhotoPathAttribute(): string
    {
        return asset('storage/' . $this->clock_in_photo_url);
        if ($this->clock_in_photo_url && Storage::disk('public')->exists($this->clock_in_photo_url)) {
            return asset('storage/' . $this->clock_in_photo_url);
        }
        return asset('img/icons/user.png');
    }

    public function getClockOutPhotoPathAttribute(): string
    {
        if ($this->clock_out_photo_url && Storage::disk('public')->exists($this->clock_out_photo_url)) {
            return asset('storage/' . $this->clock_out_photo_url);
        }
        return asset('img/icons/user.png');
    }
}
