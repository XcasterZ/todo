<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'user_id',
        'completed_at',
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
}