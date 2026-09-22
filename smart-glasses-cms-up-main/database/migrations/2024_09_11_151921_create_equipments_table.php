<?php

use App\Models\Ship;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ship::class)->comment('선박 id');
            $table->string('name')->comment('장비명');
            $table->string('type')->nullable()->comment('장비 타입');
            $table->string('model')->nullable()->comment('모델명');
            $table->string('serial_number')->nullable()->comment('시리얼번호');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
