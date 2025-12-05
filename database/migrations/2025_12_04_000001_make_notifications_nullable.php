<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make foreign keys nullable for audit/system notifications
        Schema::table('notifications', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['doctor_id']);
        });

        // SQLite doesn't support modifying columns directly, so we need to recreate
        // For now, we'll update using raw SQL for SQLite compatibility
        if (config('database.default') === 'sqlite') {
            // For SQLite, we need to recreate the table
            Schema::dropIfExists('notifications_backup');
            
            DB::statement('CREATE TABLE notifications_backup AS SELECT * FROM notifications');
            Schema::dropIfExists('notifications');
            
            Schema::create('notifications', function (Blueprint $table) {
                $table->id('notification_id');
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->string('type', 50)->default('email');
                $table->string('notification_type', 50)->default('system');
                $table->text('message');
                $table->boolean('is_sent')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->string('recipient', 255)->nullable();
                $table->timestamps();
                
                $table->index('appointment_id');
                $table->index('patient_id');
                $table->index('doctor_id');
                $table->index('is_sent');
            });
            
            DB::statement('INSERT INTO notifications SELECT * FROM notifications_backup');
            Schema::dropIfExists('notifications_backup');
        } else {
            // For MySQL/PostgreSQL
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('appointment_id')->nullable()->change();
                $table->unsignedBigInteger('patient_id')->nullable()->change();
                $table->unsignedBigInteger('doctor_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Reverse is complex - would need to delete null records first
    }
};
