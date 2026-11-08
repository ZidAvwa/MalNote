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
        Schema::table('import_entries', function (Blueprint $table) {
            $table->string('series_type')->nullable();
            $table->unsignedInteger('series_episodes')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('import_entries', function (Blueprint $table) {
            $table->dropColumn(['series_type', 'series_episodes']);
        });
    }
};
