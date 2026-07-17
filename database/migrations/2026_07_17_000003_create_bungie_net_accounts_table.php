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
        Schema::create('bungie_net_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membershipId')->unique();
            $table->string('uniqueName')->unique();
            $table->string('displayName');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bungie_net_accounts');
    }
};
