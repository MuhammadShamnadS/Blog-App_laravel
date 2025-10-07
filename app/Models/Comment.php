<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'status'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'id' => null,
            'name' => 'Deleted User',
        ]);
    }

    public function reports()
    {
        return $this->hasMany(CommentReport::class);
    }




    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->withoutGlobalScope('parentNotDeleted')
            ->with(['user', 'replies' => function ($q) {
                $q->withoutGlobalScope('parentNotDeleted')->with('user', 'replies');
            }]);
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
