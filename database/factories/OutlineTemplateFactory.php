<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OutlineTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OutlineTemplateFactory extends Factory
{
    protected $model = OutlineTemplate::class;

    public function definition(): array
    {
        return [
            'hash' => $this->faker->word(),
            'text' => $this->faker->text(),
            'active' => $this->faker->boolean(),

            'user_id' => User::factory(),
        ];
    }
}
