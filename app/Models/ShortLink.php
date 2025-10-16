<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLink extends Model
{
    protected $fillable = ['user_id', 'original_url', 'code', 'clicks'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}   