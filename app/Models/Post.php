<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content', 'author_id', 'status', 'schedule_at', 'category_id'
    ];
    protected $casts = [
        'featured' => 'boolean',
    ];

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
}

