<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Editor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
    ];

    // Optional: define relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function reviews() {
        return $this->hasMany(EditorReview::class);
    }
    protected static function booted(): void
    {
        static::addGlobalScope('parentNotDeleted', function (Builder $builder) {
            $builder->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            });
        });
    }
}
