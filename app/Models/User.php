<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'id_role',
        'profile_image',
        'phone',
        'is_lock',
        'status',
        'deleted_at'
    ];
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable'); // Định nghĩa quan hệ với Address
    }
    public function scopeList($query, $name = null)
    {
        $baseQuery = $query
            ->join('roles', 'users.id_role', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.status',
                'users.profile_image',
                'users.phone',
                'users.is_lock',
                'users.id_role',
                'roles.name as role_name'
            )
            ->orderBy('users.id', 'DESC');

        if ($name) {
            $baseQuery->where('roles.name', '=', $name);
        }

        return $baseQuery->paginate(100);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'id_role');
    }
    public function isAdmin()
    {
        if ($this->role->name == "admin") {
            return true;
        }
    }
    public function isUser()
    {
        if ($this->role->name == "user") {
            return true;
        }
    }
    public function isEmployee()
    {
        if ($this->role->name == "employee") {
            return true;
        }
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function productAudits()
    {
        return $this->hasMany(Product_audit::class, 'id_user');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'to_user_id');
    }
}
