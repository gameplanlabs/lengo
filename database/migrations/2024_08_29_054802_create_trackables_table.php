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
        Schema::create('trackables', function (Blueprint $table) {
            $table->id();
            $table->json('options')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('previous_pause_at')->nullable();
            $table->unsignedBigInteger('pause_count')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visibility_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedBigInteger('trackable_id')->nullable();
            $table->string('trackable_type')->nullable();

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
        Schema::dropIfExists('trackables');
    }
};
