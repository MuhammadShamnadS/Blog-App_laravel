<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'content', 'author_id', 'status', 'schedule_at', 'category_id'
    ];
    protected $casts = [
        'featured' => 'boolean',
    ];
    protected $dates = ['deleted_at'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function editorReview() 
    {
        return $this->hasOne(EditorReview::class);
    }
        protected static function booted(): void
    {
        static::addGlobalScope('parentNotDeleted', function (Builder $builder) {
            $builder->whereHas('author', function ($query) {
                $query->whereNull('deleted_at');
            });
        });
    }
}

