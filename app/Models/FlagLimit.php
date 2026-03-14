<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlagLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'flag_count',
    ];

    protected $casts = [
        'date' => 'date',
        'flag_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getTodayCount(int $userId): int
    {
        $today = now()->toDateString();
        
        $record = self::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        return $record ? $record->flag_count : 0;
    }

    public static function incrementFlag(int $userId): void
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            []
        )->increment('flag_count');
    }

    public static function canFlag(int $userId, int $maxFlags = 10): bool
    {
        return self::getTodayCount($userId) < $maxFlags;
    }
}
