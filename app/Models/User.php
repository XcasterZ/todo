<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'username',
        'email',
        'password', 
    ];
    
    protected $hidden = [
        'password',
    ];
    

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function completions()
    {
        return $this->hasMany(TodoCompletion::class);
    }
}