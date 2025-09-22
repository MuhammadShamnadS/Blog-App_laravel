<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
        protected $fillable = ['post_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

                protected static function booted(): void
        {
            static::addGlobalScope('parentNotDeleted', function (Builder $builder) {
                $builder->whereHas('post', function ($query) {
                    $query->whereNull('deleted_at');
                });
            });
        }
}
