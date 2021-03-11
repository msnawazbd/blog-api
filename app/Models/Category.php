<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'details',
        'status'
    ];

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }
}
