<?php

use App\Models\Task;
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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class)->comment('작업 id');
            $table->string('title')->nullable()->comment('회의 제목');
            $table->text('content')->nullable()->comment('회의 내용');
            $table->string('recordings_path')->nullable()->comment('녹화 파일 경로');
            $table->string('channel_name')->nullable()->comment('채널명');

            $table->dateTime('start_time')->nullable()->comment('시작 시간');
            $table->dateTime('end_time')->nullable()->comment('종료 시간');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
