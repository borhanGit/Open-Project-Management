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
        Schema::create('work_package_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#4f46e5');
            $table->string('icon')->default('check-circle');
            $table->boolean('is_milestone')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::create('work_package_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#64748b');
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_default')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::create('work_package_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#64748b');
            $table->boolean('is_default')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_package_priorities');
        Schema::dropIfExists('work_package_statuses');
        Schema::dropIfExists('work_package_types');
    }
};
