<?php

use App\Models\Ship;
use App\Models\ShipsCompany;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smart_glasses', function (Blueprint $table) {
            $table->comment('스마트 글래스 장비 테이블');
            $table->uuid('id')->primary();
            $table->foreignIdFor(ShipsCompany::class)->nullable()->comment('선사 id');
            $table->foreignIdFor(Ship::class)->nullable()->comment('선박 id');
            $table->string('user_name')->nullable()->comment('사용자명');
            $table->string('position')->nullable()->index()->comment('직책');
            $table->string('model')->nullable()->comment('모델명');
            $table->string('serial_number')->nullable()->index()->comment('시리얼번호');
            $table->string('firmware_version')->nullable()->comment('펌웨어 버전');
            $table->string('os_version')->nullable()->comment('OS 버전');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('상태');
            $table->string('auth_code', 8)->nullable()->unique()->comment('인증 코드');
            $table->string('fcm_token')->nullable()->comment('FCM 토큰');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_glasses');
    }
};
