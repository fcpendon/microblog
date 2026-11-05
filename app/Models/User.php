<?php

namespace App\Models;

use App\Models\Traits\HasImagePathAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasImagePathAttribute, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'image_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'post_likes', 'user_id', 'post_id');
    }
}
