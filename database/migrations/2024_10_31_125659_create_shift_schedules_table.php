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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('shift_id')->constrained('shifts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('role_id')->constrained('roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('amount')->default(0);
            $table->dateTime('notification_at')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shiftschedules');
    }
};
