<?php

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
        Schema::create('documents', function (Blueprint $table) {
            $table->comment('자료실 테이블');
            $table->id();
            $table->foreignIdFor(User::class)->comment('등록자 ID');
            $table->string('thumbnail_path')->comment('썸네일 이미지');
            $table->string('title')->comment('제목');
            $table->string('description')->nullable()->comment('설명');
            $table->string('file_name')->nullable()->comment('파일 이름');
            $table->string('file_type')->nullable()->comment('파일 타입: video, pdf');
            $table->string('file_path')->nullable()->comment('파일 경로');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
