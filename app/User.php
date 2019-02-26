<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings(){
        return $this->belongsToMany(User::class,'user_follow','user_id','follow_id')->withTimestamps();
    }
    
    public function follower(){
        return $this->belongsToMany(User::class,'user_follow','follow_id','user_id')->withTimestamps();
    }
    
    public function follow($userId){
        //既にフォローしているかの確認
        $exist = $this->is_following($userId);
        //相手が自分自身でないかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me){
            //既にフォローしている場合
            return false;
        }else{    
            //未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    public function unfollow($userId){
        //既にフォローしているかの確認
        $exist = $this->is_following($userId);
        //相手が自分自身でないかの確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me){
            //既にフォローしている場合
            $this->followings()->detach($userId);
            return true;
        }else{    
            //未フォローであればフォローする
            return false;
        }
    }
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
}
