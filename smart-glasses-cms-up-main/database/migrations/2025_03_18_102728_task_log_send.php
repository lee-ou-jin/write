<?php

use App\Models\TaskLog;
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
        Schema::create('task_log_send', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaskLog::class)->constrained('task_logs');
            $table->string('image')->nullable()->comment('이미지');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_log_send');
    }
};
