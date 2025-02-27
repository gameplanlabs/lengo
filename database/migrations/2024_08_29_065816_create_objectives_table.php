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
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->mediumText('title');
            $table->text('description')->nullable();

            $table->string('status')->default(TrackableStatus::ACTIVE)
                ->comment("Can be ['active', 'completed', 'paused', 'achieved']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('objectiveable_id')->nullable();
            $table->string('objectiveable_type')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
