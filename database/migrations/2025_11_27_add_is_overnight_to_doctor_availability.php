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
        Schema::table('doctor_availability', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_availability', 'is_overnight')) {
                $table->boolean('is_overnight')->default(false)->after('is_available')->comment('Indicates if the availability spans midnight (e.g., 5pm to 1am)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_availability', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_availability', 'is_overnight')) {
                $table->dropColumn('is_overnight');
            }
        });
    }
};
