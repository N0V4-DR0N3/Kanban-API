<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_responsibles', function (Blueprint $table) {
            $table->primaryUuid();

            $table->foreignUuid('task_id')
                ->constrained('tasks')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_responsibles');
    }
};
