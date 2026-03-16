<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\AiUsedNotification;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Override;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonInterface|null $email_verified_at
 * @property string $password
 * @property string $two_factor_secret
 * @property string $two_factor_recovery_codes
 * @property string $two_factor_confirmed_at
 * @property string $remember_token
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property UserSetting $setting
 */
final class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function notification(string $caller, string $aiClient, int $tokens, string $message): void
    {
        $notification = new AiUsedNotification($caller, $aiClient, $tokens, $message);
        $notification->onQueue('notifications');

        if (auth()->check()) {
            auth()->user()->notify($notification);

            return;
        }

        try {
            self::query()
                ->where('email', Config::string('constants.admin_email'))
                ->firstOrFail()
                ->notify($notification);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(OutlineTemplate::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
