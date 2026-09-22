<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('task_log_send', 'task_id')) {
            if (Schema::hasColumn('task_log_send', 'task_log_id')) {
                Schema::table('task_log_send', function (Blueprint $table) {
                    $table->dropForeign(['task_log_id']);
                });
            }

            Schema::table('task_log_send', function (Blueprint $table) {
                $table->unsignedBigInteger('task_id')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('task_log_send', 'task_log_id')) {
            $rows = DB::table('task_log_send')
                ->whereNotNull('task_log_id')
                ->get(['id', 'task_log_id']);

            foreach ($rows as $row) {
                $taskId = DB::table('task_logs')
                    ->where('id', $row->task_log_id)
                    ->value('task_id');

                if ($taskId && DB::table('tasks')->where('id', $taskId)->exists()) {
                    DB::table('task_log_send')
                        ->where('id', $row->id)
                        ->update(['task_id' => $taskId]);
                }
            }

            DB::table('task_log_send')->whereNull('task_id')->delete();

            Schema::table('task_log_send', function (Blueprint $table) {
                $table->dropColumn('task_log_id');
            });
        }

        Schema::table('task_log_send', function (Blueprint $table) {
            if (! $this->hasForeignKey('task_log_send', 'task_log_send_task_id_foreign')) {
                $table->foreign('task_id')
                    ->references('id')
                    ->on('tasks')
                    ->cascadeOnDelete();
            }
        });

        DB::statement('ALTER TABLE task_log_send MODIFY task_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('task_log_send', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });

        Schema::table('task_log_send', function (Blueprint $table) {
            $table->foreignId('task_log_id')
                ->constrained('task_logs');
        });
    }

    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = DB::selectOne(
            'SELECT COUNT(*) AS count FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$database, $table, $foreignKey]
        );

        return (int) ($result->count ?? 0) > 0;
    }
};
