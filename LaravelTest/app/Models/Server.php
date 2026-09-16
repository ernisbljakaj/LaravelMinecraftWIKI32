<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip',
        'version',
        'mode',
        'description',
        'approved',
        'featured',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Server $server) {
            if (empty($server->slug)) {
                $slug = Str::slug($server->name);
                $base = $slug;
                $i = 2;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }

                $server->slug = $slug;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
