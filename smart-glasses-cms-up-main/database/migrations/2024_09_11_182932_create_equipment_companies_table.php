<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('업체명');
            $table->string('address')->nullable()->comment('주소');
            $table->string('phone')->nullable()->comment('전화번호');

            $table->enum('type', ['장비업체', '관리업체'])->comment('업체 종류: 장비업체, 관리업체');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_companies');
    }
};
