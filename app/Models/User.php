<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserGender;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'location_id',
        'name',
        'email',
        'role',
        'profile_picture_url',
        'gender',
        'position',
        'telephone',
        'created_by',
        'updated_by',
        'password',
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
        'password' => 'hashed',
        'role' => UserRole::class,
        'gender' => UserGender::class,
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
    // public function location()
    // {
    //     return $this->hasOne(UserLocation::class, 'user_id');
    // }

    // public function location(): BelongsTo
    // {
    //     return $this->belongsTo(Location::class, 'location_id', 'location_id')->withDefault();
    // }
    // Di model User
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'office_location_user', // nama tabel pivot
            'user_id',             // foreign key untuk user di tabel pivot
            'location_id'          // foreign key untuk location di tabel pivot
        );
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }

    public function officeLocationUsers()
    {
        return $this->hasMany(OfficeLocationUser::class, 'user_id', 'user_id');
    }

    public function setLocationIdAttribute($value)
    {
        if (!Location::where('location_id', $value)->exists()) {
            throw new \InvalidArgumentException("ID lokasi tidak valid");
        }
        $this->attributes['location_id'] = $value;
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
    public function getOfficeNameAttribute()
    {
        return $this->locations->first()?->office_name;
    }
    public function getPhotoUrlAttribute(): string
    {
        if ($this->profile_picture_url && Storage::disk('public')->exists($this->profile_picture_url)) {
            return asset('storage/' . $this->profile_picture_url);
        }
        return asset('img/icons/user.png');
    }
}
