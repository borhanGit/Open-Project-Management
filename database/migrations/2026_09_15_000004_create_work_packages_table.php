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
        Schema::create('work_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('work_packages')->nullOnDelete();
            $table->foreignId('type_id')->constrained('work_package_types');
            $table->foreignId('status_id')->constrained('work_package_statuses');
            $table->foreignId('priority_id')->constrained('work_package_priorities');
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('subject');
            $table->longText('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->unsignedTinyInteger('done_ratio')->default(0);
            $table->integer('position')->default(0);

            $table->timestamps();

            $table->index(['project_id', 'status_id']);
            $table->index(['project_id', 'assignee_id']);
            $table->index(['project_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_packages');
    }
};
