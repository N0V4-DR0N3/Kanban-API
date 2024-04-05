<?php

use App\Enums\Auth\AuthProviders;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_oauth_tokens', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');

            $table->string('provider')
                ->index()
                ->default(AuthProviders::PASSWORD);

            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('provider_id')->nullable();
            $table->string('provider_avatar')->nullable();
            $table->string('provider_discriminator')->nullable();
            $table->string('provider_username')->nullable();
            $table->string('color')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_oauth_tokens');
    }
};
