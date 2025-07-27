<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable;

class User extends Model implements Authenticatable, CanResetPassword
{
    use \Illuminate\Auth\Authenticatable, CanResetPasswordTrait, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'userID';
    public $incrementing = false; // Since userID is a string, not auto-incrementing
    protected $keyType = 'string'; // Specify that the primary key is a string
    
    protected $fillable = [
        'userID',
        'username',
        'icNumber',
        'email',
        'role',
        'password',
        'userStatus',
        'profilePicture',
    ];

    protected $hidden = [
        'password',
    ];

    // Implement Authenticatable methods
    public function getAuthIdentifierName()
    {
        return 'userID';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function isActive()
    {
        return $this->userStatus === 'Aktif';
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the route key for the model (for route model binding)
     */
    public function getRouteKeyName()
    {
        return 'userID';
    }

    /**
     * Get the full role name for display
     */
    public function getRoleDisplayNameAttribute()
    {
        $roleNames = [
            'Admin' => 'Admin',
            'Pka' => 'Pembantu Khidmat Am',
            'pka' => 'Pembantu Khidmat Am',
        ];

        return $roleNames[$this->role] ?? $this->role;
    }
    
}