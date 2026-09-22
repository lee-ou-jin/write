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
        Schema::create('ships', function (Blueprint $table) {
            $table->comment('선박 테이블');
            $table->id();
            $table->foreignIdFor(\App\Models\ShipsCompany::class)->comment('선사 id');
            $table->string('name')->comment('선박명');
            $table->string('imo_number')->nullable()->comment('IMO 번호');
            $table->tinyInteger('type')->nullable()->comment('선박 종류, 0: 기타, 1: 화물선, 2: 여객선, 3: 유조선');
            $table->tinyInteger('status')->nullable()->comment('선박 상태, 0: 기타, 1: 운항중, 2: 정박중, 3: 수리중,');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
