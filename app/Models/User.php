<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use Uuid;
    use HasTeams;
    use TwoFactorAuthenticatable;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'role',
        'menus',
        'menu_data',
        'menu_id',
    ];

    /**
     * The roles that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }

    public function getMenuIdAttribute()
    {
        return $this->role->menus()->whereNotNull('parent_id')->pluck('menus.id')->toArray();
    }

    public function getMenusAttribute()
    {
        return $this->role->menus()->with('children')->where('parent_id')->get();
    }

    public function getMenuDataAttribute()
    {
        $role_id = $this->role->id;
        return $this->role->menus()->where('show_menu', 1)->with('children')->whereHas('roles', function ($query) use ($role_id) {
            return $query->where('role_id', $role_id);
        })->where('parent_id')->orderBy('menu_order', 'ASC')->get();
    }

    /**
     * Get the dataProdi that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dataProdi()
    {
        return $this->hasOne(DataProdi::class);
    }
}
