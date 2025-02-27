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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->mediumText('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();

            $table->string('status')->default(TrackableStatus::ACTIVE)
                ->comment("Can be ['ideation', 'planning', 'active', 'completed', 'paused', 'canceled']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->integer('estimated_time')->default(312)
                ->comment('Time in hours to complete the project.');
            $table->integer('actual_time')->default(312)
                ->comment('Total Time Taken to complete the project in hours');

            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('objective_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('progress', 3,2)->default(0.10)
                ->comment('Percentage out of 1'); // 0.10 === 10%
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('spent_budget', 10, 2)->nullable();

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
        Schema::dropIfExists('projects');
    }
};
