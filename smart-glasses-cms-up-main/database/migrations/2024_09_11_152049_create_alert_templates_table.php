<?php

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
        Schema::create('alert_templates', function (Blueprint $table) {
            $table->comment('알림 템플릿');
            $table->id();
            $table->string('name')->nullable()->comment('템플릿명');
            $table->string('description')->nullable()->comment('템플릿 설명');
            $table->string('type')->nullable()->comment('템플릿 타입');
            $table->string('title')->nullable()->comment('제목');
            $table->string('message')->nullable()->comment('메시지');
            $table->string('tag')->nullable()->comment('태그');
            $table->string('click_action')->nullable()->comment('클릭 액션');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_templates');
    }
};
