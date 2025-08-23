<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $primaryKey = 'location_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'location_id',
        'office_name',
        'check_in_time',
        'check_out_time',
        'address',
        'city',
        'latitude',
        'longitude',
        'radius',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'location_id', 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id')->withDefault();
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id')->withDefault();
    }
    // public function users(): HasMany
    // {
    //     return $this->hasMany(User::class, 'location_id', 'location_id');
    // }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'office_location_user', 'location_id', 'user_id');
    }
}
