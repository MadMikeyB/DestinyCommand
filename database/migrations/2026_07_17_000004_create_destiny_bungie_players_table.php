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
        Schema::create('destiny_bungie_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membership_id')->unique();
            $table->unsignedSmallInteger('membership_type');
            $table->string('display_name');
            $table->unsignedInteger('display_code')->nullable();
            $table->timestamps();

            $table->unique(['display_name', 'display_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destiny_bungie_players');
    }
};
