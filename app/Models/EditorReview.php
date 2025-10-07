<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EditorReview extends Model
{
    protected $fillable = ['editor_id', 'post_id', 'status', 'feedback'];

    public function editor() {
        return $this->belongsTo(Editor::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    
}
