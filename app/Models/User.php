<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
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
        return $this->morphMany(Address::class, 'addressable');
    }
    public function address()
    {
        // Đây là phương thức quan hệ, phải trả về mối quan hệ Eloquent
        return $this->morphOne(Address::class, 'addressable')
            ->where('is_default', true); // Lọc địa chỉ mặc định
    }

    public function getFullAddress()
    {
        // Lấy địa chỉ mặc định
        $address = $this->address; // Sử dụng phương thức address để lấy quan hệ

        if ($address) {
            // Thực hiện join bên ngoài để lấy thông tin đầy đủ
            return DB::table('addresses as a')
                ->join('wards as w', 'a.id_ward', '=', 'w.id')
                ->join('districts as d', 'w.district_id', '=', 'd.id')
                ->join('provinces as p', 'd.province_id', '=', 'p.id')
                ->where('a.id', '=', $address->id) // Lọc theo địa chỉ đã lấy
                ->select(
                    'a.id',
                    'a.address_detail',
                    'w.name as ward_name',
                    'w.id as ward_id',
                    'd.name as district_name',
                    'p.name as province_name',
                    'd.id as district_id',
                    'p.id as province_id'
                )
                ->first(); // Chỉ lấy 1 kết quả
        }

        return null; // Nếu không có địa chỉ mặc định
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
    public function cart()
    {
        return $this->hasOne(Cart::class, 'id_user');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_user');
    }

    public function comments()
    {
        return $this->hasMany(ProductComment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
