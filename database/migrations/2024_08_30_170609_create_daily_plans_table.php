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
        Schema::create('daily_plans', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->mediumText('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();


            $table->string('status')->default(TrackableStatus::ACTIVE)
                ->comment("Can be ['planning', 'active', 'completed', 'paused', 'canceled']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->timestamp('the_day')->comment('The date being planned for, ideally tomorrow.');
            $table->decimal('progress', 3,2)->default(0.05);
            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('dailyplannable_id')->nullable()
                ->comment('Can be Task, Todo, Activity models. Mostly activities.');
            $table->string('dailyplannable_type')->nullable(); // Polymorphic relation

            $table->text('review')->nullable()->comment('Daily review.');

            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * The idea is, time x to time y, user targets to do or accomplish something `trackable`.
         * Basically, set a target to achieve in t hours.
         * A daily target can be achieved by completing one or many App\Models\Activity.
         */
        Schema::create('daily_targets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->mediumText('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();

            $table->string('status')->default(TrackableStatus::ACTIVE)
                ->comment("Can be ['planning', 'active', 'completed', 'paused', 'canceled']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->decimal('progress', 3,2)->default(1.10);
            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->foreignId('daily_plan_id')->nullable()->constrained()
                ->cascadeOnDelete();// we can add many daily-targets to a daily-plan
            $table->foreignId('todo_id')->nullable()->constrained()
                ->nullOnDelete();// we can add many daily-targets to a to-do
            $table->foreignId('user_id')->nullable()->constrained()
                ->nullOnDelete();// we can add many daily-targets to a to-do

            $table->unsignedBigInteger('daily_targetable_id')->nullable();
            $table->string('daily_targetable_type')->nullable();

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
        Schema::dropIfExists('daily_plans');
        Schema::dropIfExists('daily_targets');
    }
};
