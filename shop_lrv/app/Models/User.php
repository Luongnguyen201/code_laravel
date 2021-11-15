<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes;
    use HasFactory, Notifiable;
    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(){
        return $this->BelongsToMany(Role::class, 'role_user', 'user_id','role_id');
    }
    public function checkPermissionAccess($permission_check){
        //user co quen sua danh muc va quen xem menu
        //buoc 1: lay duoc tat ca cac quuyen cua user dang login he thong
        //buoc 2: so sanh gia tri dua vao cua router hien tai xem co ton tai cac quen lay lai hay khong ?true/false

        $role = auth()->user()->roles;
        foreach($role as $roles){
            $permission = $roles->permissions;
            if($permission->contains('key_code', $permission_check)){
                return true;
            }
        }
        return false;
    }
}
