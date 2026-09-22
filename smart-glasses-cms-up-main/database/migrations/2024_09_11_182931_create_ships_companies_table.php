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
        Schema::create('ships_companies', function (Blueprint $table) {
            $table->id();
//            $table->foreignIdFor(User::class);
            $table->string('name')->index()->comment('회사명');
            $table->string('address')->nullable()->comment('주소');
            $table->string('phone')->nullable()->comment('전화번호');
            $table->string('email')->nullable()->comment('이메일');
            $table->string('ceo')->nullable()->comment('대표자');
            $table->string('business_number')->nullable()->comment('사업자등록번호');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships_companies');
    }
};
