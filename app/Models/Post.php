<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'id',
        'category_id',
        'created_by',
        'title',
        'details',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
