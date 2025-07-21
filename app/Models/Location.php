<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
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
}
