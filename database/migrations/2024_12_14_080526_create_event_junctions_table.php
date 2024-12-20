<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('event_junctions');
        Schema::create('event_junctions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->date('date_end');
            $table->time('time_start');
            $table->time('time_end');
            $table->text('comment')->nullable();
            $table->string('filename')->nullable();
            $table->datetime('approved_by_admin_at')->nullable();
            $table->datetime('approved_by_venue_coordinator_at')->nullable();
            $table->unsignedBigInteger('venue_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_junctions');
    }
};
