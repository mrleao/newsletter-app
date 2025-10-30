<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
        'id_user',
        'category_id',
        'image_path',
        'slug',
        'title',
        'body',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => ArticleStatus::class,
        'published_at' => 'datetime:d-m-Y  H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        if (!$this->attributes['image_path']) {
            return null;
        }
        // Se tiver 'url' configurado no disco, isso gera a URL completa
        return Storage::disk('r2')->url($this->attributes['image_path']);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }
}
