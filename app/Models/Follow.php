<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = ['guest_id', 'author_id'];


public function author()
{
    return $this->belongsTo(User::class, 'author_id');
}

public function guest()
{
    return $this->belongsTo(User::class, 'guest_id');
}


}