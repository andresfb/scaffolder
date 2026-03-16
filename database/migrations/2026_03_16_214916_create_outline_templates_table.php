<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outline_templates', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('hash');
            $table->binary('text');
            $table->boolean('active')
                ->default(true)
                ->index();

            $table->unique(['user_id', 'hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outline_templates');
    }
};
