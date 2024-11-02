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
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('department_id')->constrained('departments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('shift_type_id')->constrained('shift_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->dateTime('start_scheduling')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
