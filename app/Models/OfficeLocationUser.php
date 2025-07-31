<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocationUser extends Model
{
    use HasFactory;
    protected $table = 'office_location_user';
    protected $primaryKey = 'office_user_id';
    protected $fillable = [
        'office_location_user',
        'user_id',
        'location_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function office()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
