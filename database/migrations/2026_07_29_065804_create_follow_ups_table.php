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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();

            // Correct foreign keys
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

            // Follow-up content
            $table->text('message'); // AI question
            $table->text('response')->nullable(); // patient reply
            $table->string('status')->default('pending'); // pending / answered

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            // Drop foreign keys first (safe rollback)
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['patient_id']);
        });

        Schema::dropIfExists('follow_ups');
    }
};