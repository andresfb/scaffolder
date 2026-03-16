<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\CompressedTextCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $hash
 * @property string $text
 * @property bool $active
 */
final class OutlineTemplate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public static function getRandom(): string
    {
        return self::query()
            ->where('user_id', auth()->id())
            ->where('active', true)
            ->inRandomOrder()
            ->firstOrFail()
            ->text;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'text' => CompressedTextCast::class,
            'active' => 'boolean',
        ];
    }
}
