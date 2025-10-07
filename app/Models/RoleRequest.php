<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'requested_role', 'status', 'category_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
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
