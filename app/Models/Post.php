<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Post extends Model
{
    use HasFactory, HasUuids;

    protected $fillable=['thumbnail','title','color','slug','category_id','content','published'];

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function authors(): BelongsToMany {
        return $this->belongsToMany(User::class, 'post_user')->withTimestamps();
    }

    public function comments(): MorphMany {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
