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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->string('title');
            $table->string('slug')->unique();
            $table->mediumText('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->decimal('progress', 3,2)->default(0.05);

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default(TrackableStatus::IDEATION)
                ->comment("Can be ['ideation', 'paused', 'in-progress', 'planning', 'achieved']");
            $table->string('priority')->default(TrackablePriority::MEDIUM)
                ->comment("One of the following: ['low', 'medium', 'high']");

            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

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
        Schema::dropIfExists('goals');
    }
};
