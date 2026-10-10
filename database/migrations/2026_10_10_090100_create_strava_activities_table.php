<?php

declare(strict_types=1);

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
        Schema::create('strava_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('strava_id')->unique();
            $table->string('name');
            $table->string('sport_type');
            $table->unsignedInteger('distance_metres');
            $table->unsignedInteger('moving_seconds');
            $table->unsignedInteger('elevation_metres');
            $table->timestamp('started_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strava_activities');
    }
};
