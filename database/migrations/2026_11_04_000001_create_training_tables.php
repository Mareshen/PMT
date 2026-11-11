<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('students', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending')->index();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('student_task', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->dateTime('assigned_at');
            $table->primary(['student_id', 'task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_task');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('students');
    }
};
