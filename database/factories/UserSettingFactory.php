<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<UserSetting>
 */
final class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'value' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
            'user_id' => User::factory(),
        ];
    }
}
