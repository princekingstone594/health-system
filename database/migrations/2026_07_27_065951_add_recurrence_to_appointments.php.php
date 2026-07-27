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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('recurrence_type')->nullable(); // daily, weekly, monthly
            $table->integer('recurrence_count')->nullable(); // number of repeats
            $table->unsignedBigInteger('parent_id')->nullable(); // link group
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema:table('appointments', function (Blueprint $tabe) {
            $table->dropcolumn([
                'recurrence_type',
                'recurrence_count',
                'parent_id'
            ]);
        });
    }
};
