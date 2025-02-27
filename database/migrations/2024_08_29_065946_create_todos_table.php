<?php

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('options')->nullable();

            $table->string('status')->default(TrackableStatus::ACTIVE)
                ->comment("Can be ['planning', 'active', 'completed', 'paused', 'canceled']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->decimal('progress', 3,2)->default(0.05);
            $table->integer('estimated_time')->default(6)->comment('Time in hours');
            $table->integer('actual_time')->default(6)->comment('Time in hours');

            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
