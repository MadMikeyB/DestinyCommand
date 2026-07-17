<?php

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
        Schema::create('oauth_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('oauth_providers')->cascadeOnDelete();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->dateTime('expires_in');
            $table->dateTime('refresh_expires_in')->nullable();
            $table->string('identifier')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_sessions');
    }
};
