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
        Schema::create('import_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('original_title');
            $table->unsignedBigInteger('mal_id')->nullable();
            $table->string('final_title')->nullable();
            $table->unsignedTinyInteger('score')->default(0);
            $table->string('status')->default('Completed');
            $table->boolean('skipped')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_entries');
    }
};
