<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('game_phase')
                ->nullable()
                ->after('status');

            $table->foreignId('current_question_id')
                ->nullable()
                ->after('game_phase')
                ->constrained('questions')
                ->nullOnDelete();

            $table->unsignedBigInteger('revision')
                ->default(0)
                ->after('current_question_id');

            $table->timestamp('started_at')
                ->nullable()
                ->after('revision');

            $table->timestamp('finished_at')
                ->nullable()
                ->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign([
                'current_question_id',
            ]);

            $table->dropColumn([
                'game_phase',
                'current_question_id',
                'revision',
                'started_at',
                'finished_at',
            ]);
        });
    }
};
