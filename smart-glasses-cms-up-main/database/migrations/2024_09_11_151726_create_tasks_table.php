<?php

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('작업 코드');
            $table->foreignIdFor(\App\Models\SmartGlasses::class)->comment('작업 스마트 글래스 id');
//            $table->foreignIdFor(User::class)->comment('작업자 id');
//            $table->foreignIdFor(Equipment::class)->nullable()->comment('장비 id');
            $table->foreignIdFor(\AmuzPackages\VimeoField\Models\VimeoVideo::class)->nullable()->comment('녹화 파일 id');
            $table->string('equip_name')->nullable()->index()->comment('장비명');
            $table->string('title')->comment('작업 제목');
            $table->text('description')->nullable()->comment('작업 내용');
            $table->tinyInteger('type')->nullable()->default(0)->index()->comment('0: 기타, 1: 반복 작업, 2: 단일 작업');
            $table->enum('mode', ['online', 'offline'])->default('online')->index()->comment('모드');
            $table->enum('status', ['progress', 'complete'])->default('progress')->index()->comment('상태');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable()->comment('작업 완료일');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
