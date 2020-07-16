<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class PosUser
 */
class PosUser extends Authenticatable
{
    protected $table = 'pos_user';
    protected $primaryKey = 'user_phone';
    // protected $primaryKey = 'user_id';

    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = [
        'user_id',
        'user_nickname',
        'user_email',
        'user_password',
        'user_fullname',
        'user_avatar',
        'user_phone',
        'user_pin',
        'user_place_id',
        'user_usergroup_id',
        'user_permission',
        'user_status',
        'user_token',
        'remember_token',
        'user_login_time',
        'created_by',
        'updated_by',
        'web_device_token',
        'user_code_forgot',
        'user_lock_forgot_status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'user_token',
    ];

    protected $guarded = [];

    public function getAuthPassword() {
        return $this->user_password;
    }

    public function getRememberToken() {
        return $this->user_token;
    }

    public function setRememberToken($value)
    {
        $this->user_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'user_token';
    }
}