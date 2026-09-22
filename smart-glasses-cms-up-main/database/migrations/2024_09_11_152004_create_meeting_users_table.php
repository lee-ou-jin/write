<?php

use App\Models\Meeting;
use App\Models\User;
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
        Schema::create('meeting_users', function (Blueprint $table) {
            $table->comment('회의 참석자');
            $table->foreignIdFor(Meeting::class)->comment('회의 id');
            $table->foreignIdFor(User::class)->comment('사용자 id');
            $table->unsignedBigInteger('agora_uid')->nullable()->unique();
            $table->boolean('is_host')->default(false)->comment('회의 주최자 여부');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_users');
    }
};
