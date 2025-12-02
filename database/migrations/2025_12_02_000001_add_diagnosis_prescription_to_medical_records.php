<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('diagnosis')->nullable()->after('record_date');
            $table->text('prescription')->nullable()->after('diagnosis');
            // Make appointment_id nullable so records can be added without an appointment
            $table->foreignId('appointment_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['diagnosis', 'prescription']);
        });
    }
};
