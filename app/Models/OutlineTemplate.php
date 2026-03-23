<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\CompressedTextCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;
use Random\RandomException;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $hash
 * @property string $title
 * @property string $text
 * @property bool $active
 * @property int $weight
 */
final class OutlineTemplate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * @throws RandomException
     */
    public static function getRandom(): self
    {
        $templates = self::query()
            ->where('user_id', auth()->id())
            ->where('active', true)
            ->get();

        if ($templates->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(self::class);
        }

        $totalWeight = $templates->sum('weight');
        $random = random_int(1, $totalWeight);
        $cumulativeWeight = 0;

        foreach ($templates as $template) {
            $cumulativeWeight += $template->weight;

            if ($random <= $cumulativeWeight) {
                return $template;
            }
        }

        return $templates->last();
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
